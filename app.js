(function () {
  'use strict';

  const app = document.querySelector('#app');
  const courses = window.UnikonCurriculum;
  const stateModel = window.UnikonState;
  const SESSION_KEY = 'unikon-demo-session';
  let activeCourse = null;
  let pendingCourseId = null;

  const escapeHtml = (value) => String(value).replace(/[&<>"]/g, (character) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[character]));
  const signedIn = () => sessionStorage.getItem(SESSION_KEY) === 'webmcp_judge';
  const stateKey = (id) => `unikon-progress:webmcp_judge:${id}`;

  function getState(course = activeCourse) {
    if (!course) return null;
    try {
      const state=stateModel.normalize(JSON.parse(localStorage.getItem(stateKey(course.id)) || '{}'));
      const firstIncomplete=course.assessments.find((assessment)=>state.activityStatuses[assessment.id]!=='completed');
      if(state.exerciseStatus==='completed'&&firstIncomplete){state.exerciseStatus='in_progress';state.activityStatuses[firstIncomplete.id]='in_progress';saveState(state,course);}
      return state;
    }
    catch (_) { return stateModel.defaults(); }
  }

  function saveState(state, course = activeCourse) {
    localStorage.setItem(stateKey(course.id), JSON.stringify(state));
  }

  function summary(course, state) {
    if (state.exerciseStatus === 'completed') return {percent: 100, next_step: {action:'complete', label:'Course complete—review what you learned.'}};
    if (state.exerciseStatus === 'in_progress') {
      const completed = Object.values(state.activityStatuses).filter((status) => status === 'completed').length;
      return {percent: Math.min(95, 40 + Math.floor(55 * completed / course.assessments.length)), next_step:{action:'submit_answer',label:'Complete the current exercise.'}};
    }
    if (state.lessonStatus !== 'not_started') return {percent:35,next_step:{action:'start_exercise',label:'Start the current course exercise.'}};
    return {percent:0,next_step:{action:'open_lesson',label:'Open your first lesson.'}};
  }

  function publicState(course = activeCourse, state = getState(course)) {
    const progress = summary(course, state);
    const assessments=course.assessments.map((assessment,index)=>({id:assessment.id,title:assessment.title,status:stateModel.statusFor(state,assessment.id,index)}));
    const currentIndex=assessments.findIndex((assessment)=>assessment.status==='in_progress');
    const current=currentIndex>=0 ? course.assessments[currentIndex] : null;
    return {
      course: {id: course.id, title: course.title},
      lesson: {title: course.lesson.title, status: state.lessonStatus},
      exercise: {status: state.exerciseStatus},
      assessments,
      current_assessment: current ? {id:current.id,title:current.title,prompt:current.prompt,type:current.type,choices:Object.entries(current.choices).map(([id,label])=>({id,label})),min_length:current.minLength,max_length:current.maxLength} : null,
      allowed_actions: progress.next_step.action==='open_lesson' ? ['open_next_lesson'] : progress.next_step.action==='start_exercise' ? ['start_exercise'] : progress.next_step.action==='submit_answer' ? ['stage_exercise_answer'] : [],
      submission_count: state.submissions.length,
      progress
    };
  }

  function homeView() {
    activeCourse = null;
    const cards = Object.values(courses).map((course, index) => `<article><p class="uwmcp-home-course-number">0${index + 1}</p><h3>${escapeHtml(course.title)}</h3><p>${escapeHtml(course.description)}</p><a href="#course/${course.id}">Open course</a></article>`).join('');
    app.innerHTML = `<main class="uwmcp-home"><section class="uwmcp-home-hero" aria-labelledby="home-title"><img class="uwmcp-home-hero-image" src="assets/fashion-elearning-unikon.webp" alt="A fashion designer fitting a blue garment on a dress form."><div class="uwmcp-home-hero-shade"></div><div class="uwmcp-home-hero-content"><p class="uwmcp-eyebrow">Fashion eSchool</p><h1 id="home-title">Learn interactively. Build real fashion skills.</h1><p>Explore fashion design and practical sewing through structured lessons and guided exercises.</p><div class="uwmcp-home-actions"><a class="uwmcp-home-button" href="${signedIn() ? '#course/fashion-foundations' : '#login'}">${signedIn() ? 'Enter the learning studio' : 'Log in to try the demo'}</a><a class="uwmcp-home-text-link" href="https://github.com/ginanisque/unikon-webmcp-demo">View the open-source project</a></div></div></section><section class="uwmcp-home-intro"><div><p class="uwmcp-eyebrow">Human-first by design</p><h2>Three ways to learn</h2></div><p>Get help finding your next lesson, understanding instructions, and preparing answers. You review everything and decide what to submit.</p></section><div class="uwmcp-home-courses">${cards}</div><section class="uwmcp-home-boundary"><p class="uwmcp-eyebrow">A clear boundary</p><h2>You learn. Your assistant guides.</h2><p>Your learning assistant can explain instructions and help prepare a response, but you remain responsible for practical work and submission.</p></section></main>`;
    document.dispatchEvent(new CustomEvent('unikon:view-change',{detail:{courseId:null}}));
  }

  function assessmentView(assessment, status) {
    const choices = Object.entries(assessment.choices).map(([value,label]) => `<label class="uwmcp-choice"><input type="radio" name="answer_id" value="${value}" required> <span>${escapeHtml(label)}</span></label>`).join('');
    const reading=assessment.content ? `<div class="uwmcp-topic-note"><strong>Technique guide</strong><p>${escapeHtml(assessment.content)}</p></div>` : '';
    const guidance=`Write ${assessment.minLength}–${assessment.maxLength} characters${assessment.type==='essay' ? ' and connect at least three ideas from the lesson' : ' and support your response with a specific idea from the lesson'}.`;
    return `<article class="uwmcp-assessment" data-assessment="${assessment.id}" data-status="${status}" ${status === 'locked' ? 'hidden' : ''}><p class="uwmcp-kicker">${assessment.type.replace('_',' ')}</p><h3>${escapeHtml(assessment.title)}</h3>${reading}<p>${escapeHtml(assessment.prompt)}</p>${status === 'completed' ? '<p class="uwmcp-complete-label">Passed</p>' : ''}<form data-exercise-form data-activity-id="${assessment.id}" ${status === 'completed' ? 'hidden' : ''}>${choices ? `<fieldset><legend>Choose one answer</legend>${choices}</fieldset>` : ''}<label><strong>${assessment.type === 'essay' ? 'Write your essay' : 'Explain your response'}</strong><textarea name="reason" rows="${assessment.type === 'essay' ? 10 : 4}" minlength="${assessment.minLength}" maxlength="${assessment.maxLength}" aria-describedby="guide-${assessment.id} count-${assessment.id}" required></textarea></label><div class="uwmcp-response-meta"><p id="guide-${assessment.id}">${guidance}</p><p id="count-${assessment.id}" data-character-count>0 / ${assessment.maxLength}</p></div><div class="uwmcp-confirmation" data-confirmation hidden tabindex="-1"><strong>Ready for your review</strong><p>Your learning assistant prepared this response. Nothing is graded or saved until you choose Submit my answer.</p></div><button class="uwmcp-button" type="submit">Submit my answer</button></form><div class="uwmcp-feedback" data-feedback hidden tabindex="-1"></div></article>`;
  }

  function courseView(id) {
    const course = courses[id] || courses['fashion-foundations'];
    activeCourse = course;
    if (!signedIn()) { pendingCourseId=course.id; homeView(); history.replaceState(null,'',`#course/${course.id}`); openLogin(); return; }
    const state = getState(course);
    const view = publicState(course, state);
    const nav = Object.values(courses).map((item) => `<a href="#course/${item.id}" ${item.id === course.id ? 'aria-current="page"' : ''}>${escapeHtml(item.title)}</a>`).join('');
    const assessments = course.assessments.map((assessment, index) => assessmentView(assessment, view.assessments[index].status)).join('');
    app.innerHTML = `<main class="uwmcp-app" data-uwmcp-app data-authenticated="true" data-course-id="${course.id}"><header class="uwmcp-hero"><nav class="uwmcp-course-nav" aria-label="Courses">${nav}</nav><figure class="uwmcp-hero-visual"><img src="${course.hero}" alt="${escapeHtml(course.title)}"></figure><p class="uwmcp-eyebrow">Interactive fashion learning</p><h1>${escapeHtml(course.title)}</h1><p>${escapeHtml(course.description)}</p><div class="uwmcp-agent-status" data-agent-status><span></span>Learning-assistant tools are checking browser support…</div><button class="uwmcp-reset" type="button" data-reset-progress>Reset this course</button></header><div class="uwmcp-live sr-only" aria-live="polite" data-live-region></div><section class="uwmcp-progress"><div><p class="uwmcp-eyebrow">Your progress</p><h2><span data-progress-value>${view.progress.percent}</span>%</h2></div><div class="uwmcp-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${view.progress.percent}" data-progress-bar><span style="width:${view.progress.percent}%"></span></div><p data-next-step>${view.progress.next_step.label}</p></section><section class="uwmcp-card" data-lesson-section><p class="uwmcp-step">01</p><div><p class="uwmcp-kicker">One concise lesson</p><h2>${escapeHtml(course.lesson.title)}</h2><p><strong>Objective:</strong> ${escapeHtml(course.lesson.objective)}</p><div class="uwmcp-lesson-body" data-lesson-body ${state.lessonStatus === 'not_started' ? 'hidden' : ''}>${course.lesson.body.map((p) => `<p>${escapeHtml(p)}</p>`).join('')}</div><button class="uwmcp-button" data-action="open-lesson" ${state.lessonStatus !== 'not_started' ? 'hidden' : ''}>Open lesson</button><button class="uwmcp-button uwmcp-button-secondary" data-action="start-exercise" ${state.lessonStatus === 'not_started' || state.exerciseStatus !== 'not_started' ? 'hidden' : ''}>Start exercise</button></div></section><section class="uwmcp-card" data-exercise-section ${state.exerciseStatus === 'not_started' ? 'hidden' : ''}><p class="uwmcp-step">02</p><div><p class="uwmcp-kicker">Apply what you learned</p><h2>Guided assessment</h2><p>Complete each layer in order. Passed work unlocks the next task.</p><p class="uwmcp-submission-count"><strong>${state.submissions.length}</strong> submitted answers</p><div class="uwmcp-assessment-list">${assessments}</div></div></section></main>`;
    bindCourseEvents();
    document.dispatchEvent(new CustomEvent('unikon:view-change',{detail:{courseId:course.id}}));
  }

  function announce(message) { const region = app.querySelector('[data-live-region]'); if (region) region.textContent = message; }
  function refresh(message, feedback, draft) { const id = activeCourse.id; courseView(id); if(draft){const form=app.querySelector(`[data-activity-id="${CSS.escape(draft.activityId)}"]`);if(form){form.elements.reason.value=draft.reason;if(draft.answerId){const radio=form.querySelector(`[name="answer_id"][value="${CSS.escape(draft.answerId)}"]`);if(radio)radio.checked=true;}updateCharacterCount(form.elements.reason);}} if (feedback) { const panel=app.querySelector(`[data-assessment="${CSS.escape(feedback.activityId)}"] [data-feedback]`); if(panel){panel.hidden=false;panel.classList.toggle('is-success',feedback.passed);panel.textContent=feedback.message;panel.focus();} } if (message) announce(message); }
  function openLesson() { const state=stateModel.openLesson(getState()); saveState(state); refresh('Lesson opened.'); return publicState(); }
  function startExercise() { const state=stateModel.startExercise(getState(),activeCourse.assessments[0].id); saveState(state); refresh('Exercise started.'); return publicState(); }

  function stageAnswer(activityId, answerId, reason) {
    if(typeof activityId!=='string'||typeof reason!=='string'||reason.trim().length<12||reason.trim().length>1200) throw Object.assign(new Error('Provide a valid activity and a response between 12 and 1200 characters.'),{code:'invalid_parameters'});
    const card=app.querySelector(`[data-assessment="${CSS.escape(activityId)}"]`);
    const form=card&&card.dataset.status==='in_progress'&&!card.hidden ? card.querySelector('[data-exercise-form]') : null;
    if (!form) throw Object.assign(new Error('The exercise is not currently available.'),{code:'invalid_state'});
    const radio=answerId ? form.querySelector(`[name="answer_id"][value="${CSS.escape(answerId)}"]`) : null;
    if (answerId && !radio) throw Object.assign(new Error('That answer is not available.'),{code:'invalid_parameters'});
    if (radio) radio.checked=true; form.elements.reason.value=reason.trim(); updateCharacterCount(form.elements.reason);
    const panel=form.querySelector('[data-confirmation]'); panel.hidden=false; panel.focus();
    announce('Answer staged for review.'); return {staged:true,committed:false,message:'Learner confirmation is required.'};
  }

  function bindCourseEvents() {
    app.querySelectorAll('[data-action]').forEach((button)=>button.addEventListener('click',()=>button.dataset.action==='open-lesson' ? openLesson() : startExercise()));
    app.querySelectorAll('[data-exercise-form]').forEach((form)=>form.addEventListener('submit',(event)=>{
      event.preventDefault(); if (!form.reportValidity()) return;
      const fields=new FormData(form); let result;
      try { result=stateModel.submit(getState(),activeCourse,form.dataset.activityId,fields.get('answer_id')||'',fields.get('reason'),new Date().toISOString()); }
      catch(error){announce(error.message);return;}
      saveState(result.state); const draft=result.evaluation.passed ? null : {activityId:form.dataset.activityId,answerId:fields.get('answer_id')||'',reason:fields.get('reason')}; refresh(result.evaluation.feedback,{activityId:form.dataset.activityId,passed:result.evaluation.passed,message:result.evaluation.feedback},draft);
    }));
    app.querySelectorAll('textarea[maxlength]').forEach((textarea)=>textarea.addEventListener('input',()=>updateCharacterCount(textarea)));
    const reset=app.querySelector('[data-reset-progress]');
    if(reset)reset.addEventListener('click',()=>{if(window.confirm('Reset all progress and attempts for this course?')){localStorage.removeItem(stateKey(activeCourse.id));refresh('Course progress reset.');}});
  }

  function updateCharacterCount(textarea){const counter=textarea.form&&textarea.form.querySelector('[data-character-count]');if(counter)counter.textContent=`${textarea.value.length} / ${textarea.maxLength}`;}

  function openLogin(){const error=loginForm.querySelector('[data-login-error]');error.hidden=true;error.textContent='';if(!loginDialog.open)loginDialog.showModal();}
  function leaveLogin(){loginDialog.close();if(!signedIn()&&(location.hash==='#login'||location.hash.startsWith('#course/')))location.hash='home';}
  function route() { const hash=location.hash.slice(1); if(hash==='login'){openLogin();return;} if(hash.startsWith('course/')) courseView(hash.split('/')[1]); else homeView(); }
  const loginDialog=document.querySelector('[data-login-dialog]'); const loginForm=document.querySelector('[data-login-form]');
  document.querySelector('[data-login-button]').addEventListener('click',openLogin);
  document.querySelector('[data-dialog-close]').addEventListener('click',leaveLogin);
  loginDialog.addEventListener('cancel',(event)=>{event.preventDefault();leaveLogin();});
  document.querySelector('[data-logout-button]').addEventListener('click',()=>{sessionStorage.removeItem(SESSION_KEY); updateAuthButtons(); location.hash='home';});
  loginForm.addEventListener('submit',(event)=>{event.preventDefault();const fields=new FormData(loginForm);if(fields.get('username')==='webmcp_judge'&&fields.get('password')==='demo_judge'){sessionStorage.setItem(SESSION_KEY,'webmcp_judge');loginDialog.close();loginForm.reset();updateAuthButtons();const target=pendingCourseId||((location.hash.startsWith('#course/'))?location.hash.split('/')[1]:null)||'fashion-foundations';pendingCourseId=null;const nextHash=`#course/${target}`;if(location.hash===nextHash)route();else location.hash=nextHash;}else{const error=loginForm.querySelector('[data-login-error]');error.textContent='The username or password is incorrect.';error.hidden=false;}});
  function updateAuthButtons(){document.querySelector('[data-login-button]').hidden=signedIn();document.querySelector('[data-logout-button]').hidden=!signedIn();}
  window.UnikonLearningApp={getState:()=>activeCourse ? publicState() : null,getActiveCourse:()=>activeCourse,openLesson,startExercise,stageAnswer,summary:()=>activeCourse ? summary(activeCourse,getState()) : null};
  addEventListener('hashchange',route); updateAuthButtons(); route();
}());
