const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

function loadTools() {
  const window = { addEventListener() {}, UnikonLearningApp: null };
  const document = { querySelector() { return null; } };
  const context = vm.createContext({ AbortController, console, document, window });
  const source = fs.readFileSync(path.join(__dirname, '../../public/js/webmcp-tools.js'), 'utf8');
  vm.runInContext(source, context);
  return context.window.UnikonWebMCPTools;
}

function fakeRoot() {
  const status = { classList: { toggle() {} }, lastChild: { textContent: '' } };
  return {
    dataset: { authenticated: 'true' },
    querySelector(selector) { return selector === '[data-agent-status]' ? status : null; },
    status,
  };
}

test('defines exactly the five approved tools with strict schemas', () => {
  const tools = loadTools();
  const definitions = tools.definitions({});
  assert.deepEqual(Array.from(definitions, (tool) => tool.name), Array.from(tools.TOOL_NAMES));
  assert.equal(definitions.length, 5);
  for (const tool of definitions) assert.equal(tool.inputSchema.additionalProperties, false);
  assert.deepEqual({ ...definitions[0].annotations }, { readOnlyHint: true });
  assert.deepEqual({ ...definitions[3].annotations }, { readOnlyHint: false, untrustedContentHint: true });
});

test('awaits registrations and cleanup aborts the shared signal', async () => {
  const tools = loadTools();
  const registered = [];
  const modelContext = {
    async registerTool(tool, options) {
      assert.equal(options.signal.aborted, false);
      registered.push(tool.name);
    },
  };
  const result = await tools.init({ document: {}, root: fakeRoot(), app: {}, modelContext });
  assert.deepEqual(Array.from(result.registered), Array.from(tools.TOOL_NAMES));
  assert.deepEqual(registered, Array.from(tools.TOOL_NAMES));
  result.cleanup();
  assert.equal(result.controller.signal.aborted, true);
});

test('registration failure aborts partial registration without breaking the page', async () => {
  const tools = loadTools();
  let count = 0;
  const result = await tools.init({
    document: {},
    root: fakeRoot(),
    app: {},
    modelContext: { async registerTool() { count += 1; if (count === 2) throw new Error('blocked'); } },
  });
  assert.equal(result.supported, true);
  assert.equal(result.registered.length, 0);
  assert.equal(result.error.message, 'blocked');
});

test('staging changes only the visible form adapter', async () => {
  const tools = loadTools();
  let staged;
  let requests = 0;
  const app = {
    stageAnswer(answerId, reason) { staged = { answerId, reason }; return { staged: true, committed: false }; },
    request() { requests += 1; },
  };
  const tool = tools.definitions(app).find((item) => item.name === 'stage_exercise_answer');
  const result = await tool.execute({ answer_id: 'cotton-poplin', reason: 'Stable and light enough to hold the silhouette.' });
  assert.deepEqual(staged, { answerId: 'cotton-poplin', reason: 'Stable and light enough to hold the silhouette.' });
  assert.equal(requests, 0);
  assert.equal(result.structuredContent.committed, false);
});

test('invalid staged answers return an MCP error result', async () => {
  const tools = loadTools();
  const tool = tools.definitions({ stageAnswer() { throw new Error('must not run'); } }).find((item) => item.name === 'stage_exercise_answer');
  const result = await tool.execute({ answer_id: 'unknown', reason: 'too short' });
  assert.equal(result.isError, true);
  assert.equal(result.structuredContent.code, 'invalid_parameters');
});
