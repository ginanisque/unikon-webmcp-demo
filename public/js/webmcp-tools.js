(function () {
  'use strict';

  const TOOL_NAMES = [
    'get_learning_state',
    'open_next_lesson',
    'start_exercise',
    'stage_exercise_answer',
    'get_progress_and_next_step',
  ];

  const emptySchema = { type: 'object', properties: {}, additionalProperties: false };
  const ACTIVITY_IDS = [
    'fabric-choice', 'design-signal', 'colour-story', 'silhouette-analysis', 'material-direction', 'moodboard-edit', 'collection-rationale',
    'threading-machine', 'machine-tension', 'guide-fabric', 'practice-preparation', 'straight-lines', 'curved-lines', 'angled-lines',
    'science-repetition', 'basic-zipper', 'invisible-zipper', 'decorative-zipper', 'front-fly-zipper', 'topstitch-understitch',
    'reinforce-stitching', 'ease-gathers', 'sew-darts', 'sew-corners', 'plackets', 'blind-hemming',
  ];

  function mcpResult(data, message) {
    return {
      content: [{ type: 'text', text: message }],
      structuredContent: data,
    };
  }

  function mcpError(error) {
    const code = error && error.code ? error.code : 'tool_failed';
    const message = error && error.message ? error.message : 'The tool could not complete the request.';
    return {
      content: [{ type: 'text', text: code + ': ' + message }],
      structuredContent: { code, message },
      isError: true,
    };
  }

  function validateEmpty(input) {
    if (!input || typeof input !== 'object' || Array.isArray(input) || Object.keys(input).length > 0) {
      throw Object.assign(new Error('This tool does not accept parameters.'), { code: 'invalid_parameters' });
    }
  }

  function validateStagedAnswer(input) {
    const allowed = ['activity_id', 'answer_id', 'reason'];
    if (!input || typeof input !== 'object' || Array.isArray(input) || Object.keys(input).some((key) => !allowed.includes(key))) {
      throw Object.assign(new Error('Use only activity_id, answer_id, and reason.'), { code: 'invalid_parameters' });
    }
    if (!ACTIVITY_IDS.includes(input.activity_id)) {
      throw Object.assign(new Error('Choose the current unlocked activity_id.'), { code: 'invalid_parameters' });
    }
    if (input.answer_id !== undefined && typeof input.answer_id !== 'string') {
      throw Object.assign(new Error('answer_id must be a listed identifier or omitted for an essay.'), { code: 'invalid_parameters' });
    }
    const answers = ['', 'cotton-poplin', 'silk-charmeuse', 'heavy-denim', 'repeated-curves', 'everything-coastal', 'current-trends', 'sand-blue-foam', 'rainbow', 'neon-metallic', 'coastal-movement', 'mixed-trends', 'logo-study'];
    if (input.answer_id !== undefined && !answers.includes(input.answer_id)) {
      throw Object.assign(new Error('answer_id must match a choice in the visible assessment.'), { code: 'invalid_parameters' });
    }
    if (typeof input.reason !== 'string' || input.reason.trim().length < 12 || input.reason.trim().length > 1200) {
      throw Object.assign(new Error('Reason must contain 12 to 1200 characters.'), { code: 'invalid_parameters' });
    }
    return { activity_id: input.activity_id, answer_id: input.answer_id || '', reason: input.reason.trim() };
  }

  function definitions(app) {
    return [
      {
        name: 'get_learning_state',
        description: 'Read the signed-in learner’s current lesson, exercise state, progress, and allowed next actions. Use this before choosing a learning action.',
        inputSchema: emptySchema,
        annotations: { readOnlyHint: true },
        async execute(input) {
          try {
            validateEmpty(input);
            const data = await app.request('state');
            return mcpResult(data, 'Learning state loaded. Next: ' + data.progress.next_step.label);
          } catch (error) { return mcpError(error); }
        },
      },
      {
        name: 'open_next_lesson',
        description: 'Open the learner’s available lesson and show it visibly. This may mark an unopened lesson as in progress; repeated calls are safe.',
        inputSchema: emptySchema,
        annotations: { readOnlyHint: false },
        async execute(input) {
          try {
            validateEmpty(input);
            const data = await app.openLesson();
            return mcpResult(data, 'Lesson opened visibly: ' + data.lesson.title);
          } catch (error) { return mcpError(error); }
        },
      },
      {
        name: 'start_exercise',
        description: 'Start and visibly open the exercise for the current lesson. The lesson must be opened first. Repeated calls are safe.',
        inputSchema: emptySchema,
        annotations: { readOnlyHint: false },
        async execute(input) {
          try {
            validateEmpty(input);
            const data = await app.startExercise();
            return mcpResult(data, 'Exercise started visibly: ' + data.exercise.title);
          } catch (error) { return mcpError(error); }
        },
      },
      {
        name: 'stage_exercise_answer',
        description: 'Stage a proposed answer in one visible unlocked assessment for learner review. This never grades, submits, or saves the response.',
        inputSchema: {
          type: 'object',
          properties: {
            activity_id: {
              type: 'string',
              enum: ACTIVITY_IDS,
              description: 'Identifier of the current unlocked assessment layer.',
            },
            answer_id: {
              type: 'string',
              enum: ['', 'cotton-poplin', 'silk-charmeuse', 'heavy-denim', 'repeated-curves', 'everything-coastal', 'current-trends', 'sand-blue-foam', 'rainbow', 'neon-metallic', 'coastal-movement', 'mixed-trends', 'logo-study'],
              description: 'Listed choice identifier; use an empty string for text-only work.',
            },
            reason: {
              type: 'string',
              minLength: 12,
              maxLength: 1200,
              description: 'Plain-text response or essay addressing the assessment prompt.',
            },
          },
          required: ['activity_id', 'reason'],
          additionalProperties: false,
        },
        annotations: { readOnlyHint: false, untrustedContentHint: true },
        async execute(input) {
          try {
            const answer = validateStagedAnswer(input);
            const result = app.stageAnswer(answer.activity_id, answer.answer_id, answer.reason);
            return mcpResult(result, 'Answer staged for review. The learner must click “Submit my answer” to grade and save it.');
          } catch (error) { return mcpError(error); }
        },
      },
      {
        name: 'get_progress_and_next_step',
        description: 'Read the signed-in learner’s completion percentage, completed milestones, and exactly one recommended next step.',
        inputSchema: emptySchema,
        annotations: { readOnlyHint: true },
        async execute(input) {
          try {
            validateEmpty(input);
            const data = await app.request('progress');
            return mcpResult(data, data.percent + '% complete. Next: ' + data.next_step.label);
          } catch (error) { return mcpError(error); }
        },
      },
    ];
  }

  function setStatus(root, state, message) {
    const status = root && root.querySelector('[data-agent-status]');
    if (!status) return;
    status.classList.toggle('is-ready', state === 'ready');
    status.lastChild.textContent = message;
  }

  async function init(options = {}) {
    const doc = options.document || document;
    const root = options.root || doc.querySelector('[data-uwmcp-app]');
    const app = options.app || window.UnikonLearningApp;
    const modelContext = options.modelContext || doc.modelContext;

    if (!root || root.dataset.authenticated !== 'true' || !app) return { supported: false, registered: [] };
    if (!modelContext || typeof modelContext.registerTool !== 'function') {
      setStatus(root, 'unsupported', 'WebMCP unavailable; the learning interface still works normally.');
      return { supported: false, registered: [] };
    }

    const controller = new AbortController();
    const registered = [];
    try {
      for (const tool of definitions(app)) {
        await modelContext.registerTool(tool, { signal: controller.signal });
        registered.push(tool.name);
      }
      setStatus(root, 'ready', 'Five WebMCP learning tools are ready.');
    } catch (error) {
      controller.abort();
      setStatus(root, 'error', 'WebMCP registration was blocked; the learning interface still works normally.');
      return { supported: true, registered: [], error };
    }

    const cleanup = () => controller.abort();
    window.addEventListener('pagehide', cleanup, { once: true });
    return { supported: true, registered, controller, cleanup };
  }

  window.UnikonWebMCPTools = { TOOL_NAMES, ACTIVITY_IDS, definitions, init, validateEmpty, validateStagedAnswer };
  init().catch(() => {});
}());
