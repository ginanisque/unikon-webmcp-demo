const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function tools() {
  const course={assessments:[{id:'fabric-choice',choices:{cotton:'Cotton'}}]};
  const listeners={};
  const window={UnikonCurriculum:{course},addEventListener(){}};
  const document={querySelector(){return null;},addEventListener(name,handler){listeners[name]=handler;}};
  const context={window,document,AbortController,setTimeout(){return 1;},clearTimeout(){},Set,Object,Array,Error};
  vm.runInNewContext(fs.readFileSync('webmcp.js','utf8'),context);
  return window.UnikonWebMCPTools;
}

test('defines exactly five tools with strict staged-answer enums', () => {
  const definitions=tools().definitions({});
  assert.equal(definitions.length,5);
  const staged=definitions.find((tool)=>tool.name==='stage_exercise_answer');
  assert.deepEqual(Array.from(staged.inputSchema.properties.activity_id.enum),['fabric-choice']);
  assert.equal(staged.inputSchema.additionalProperties,false);
});

test('rejects unknown and malformed staged answers', () => {
  const api=tools();
  assert.throws(()=>api.validateAnswer({activity_id:'locked',reason:'Long enough response'}),/listed activity/);
  assert.throws(()=>api.validateAnswer({activity_id:'fabric-choice',reason:'short'}),/12 to 1200/);
});
