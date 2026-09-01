(function () {
  'use strict';

  const root = document.querySelector('[data-uwmcp-app]');
  const config = window.UnikonWebMCPDemo;
  if (!root || !config) return;

  const live = root.querySelector('[data-live-region]');

  async function request(path, options = {}) {
    const response = await fetch(config.root + path, {
      credentials: 'same-origin',
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': config.nonce,
        ...(options.headers || {}),
      },
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      const error = new Error(data.message || 'The learning service could not complete that request.');
      error.code = data.code || 'request_failed';
      throw error;
    }
    return data;
  }

  function announce(message) {
    if (live) live.textContent = '';
    window.setTimeout(() => { if (live) live.textContent = message; }, 20);
  }

  function updateProgress(progress) {
    if (!progress) return;
    const value = root.querySelector('[data-progress-value]');
    const bar = root.querySelector('[data-progress-bar]');
    const next = root.querySelector('[data-next-step]');
    if (value) value.textContent = String(progress.percent);
    if (bar) {
      bar.setAttribute('aria-valuenow', String(progress.percent));
      const fill = bar.querySelector('span');
      if (fill) fill.style.width = progress.percent + '%';
    }
    if (next && progress.next_step) next.textContent = progress.next_step.label;
  }

  function applyState(payload, focusTarget) {
    const lesson = root.querySelector('[data-lesson-section]');
    const lessonBody = root.querySelector('[data-lesson-body]');
    const openButton = root.querySelector('[data-action="open-lesson"]');
    const startButton = root.querySelector('[data-action="start-exercise"]');
    const exercise = root.querySelector('[data-exercise-section]');
    if (lesson) lesson.dataset.status = payload.lesson.status;
    if (lessonBody) lessonBody.hidden = payload.lesson.status === 'not_started';
    if (openButton) openButton.hidden = payload.lesson.status !== 'not_started';
    if (startButton) startButton.hidden = payload.lesson.status === 'not_started' || payload.exercise.status === 'completed';
    if (exercise) {
      exercise.dataset.status = payload.exercise.status;
      exercise.hidden = payload.exercise.status === 'not_started';
    }
    updateProgress(payload.progress);
    if (focusTarget) {
      const target = root.querySelector(focusTarget);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        const heading = target.querySelector('h2');
        if (heading) { heading.tabIndex = -1; heading.focus({ preventScroll: true }); }
      }
    }
    root.dispatchEvent(new CustomEvent('unikon:state', { detail: payload }));
  }

  async function openLesson() {
    const data = await request('lesson/open', { method: 'POST', body: '{}' });
    applyState(data, '[data-lesson-section]');
    announce('Lesson opened.');
    return data;
  }

  async function startExercise() {
    const data = await request('exercise/start', { method: 'POST', body: '{}' });
    applyState(data, '[data-exercise-section]');
    announce('Exercise started.');
    return data;
  }

  function stageAnswer(answerId, reason) {
    const form = root.querySelector('[data-exercise-form]');
    if (!form) throw Object.assign(new Error('The exercise is not available.'), { code: 'invalid_state' });
    const radio = form.querySelector('input[name="answer_id"][value="' + CSS.escape(answerId) + '"]');
    const textarea = form.querySelector('[name="reason"]');
    if (!radio || !textarea) throw Object.assign(new Error('The proposed answer is not valid.'), { code: 'invalid_parameters' });
    radio.checked = true;
    textarea.value = reason;
    const panel = form.querySelector('[data-confirmation]');
    if (panel) { panel.hidden = false; panel.focus(); }
    announce('Answer staged for review. Submit my answer to grade and save it.');
    return { staged: true, committed: false, message: 'Answer staged. Learner confirmation is required.' };
  }

  root.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-action]');
    if (!button) return;
    button.disabled = true;
    try {
      if (button.dataset.action === 'open-lesson') await openLesson();
      if (button.dataset.action === 'start-exercise') await startExercise();
    } catch (error) {
      announce(error.message);
    } finally {
      button.disabled = false;
    }
  });

  const form = root.querySelector('[data-exercise-form]');
  if (form) form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const button = form.querySelector('[data-submit-answer]');
    const feedback = root.querySelector('[data-feedback]');
    button.disabled = true;
    try {
      const fields = new FormData(form);
      const data = await request('exercise/submit', {
        method: 'POST',
        body: JSON.stringify({ answer_id: fields.get('answer_id'), reason: fields.get('reason') }),
      });
      applyState(data.state);
      if (feedback) {
        feedback.hidden = false;
        feedback.classList.toggle('is-success', data.evaluation.passed);
        feedback.textContent = data.evaluation.feedback;
        feedback.focus();
      }
      const confirmation = form.querySelector('[data-confirmation]');
      if (confirmation) confirmation.hidden = true;
      announce(data.evaluation.feedback);
    } catch (error) {
      announce(error.message);
      if (feedback) { feedback.hidden = false; feedback.textContent = error.message; feedback.focus(); }
    } finally {
      button.disabled = false;
    }
  });

  window.UnikonLearningApp = { request, applyState, openLesson, startExercise, stageAnswer, announce };
}());

