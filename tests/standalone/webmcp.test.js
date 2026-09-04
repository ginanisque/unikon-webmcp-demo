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

test('defines exactly five tools including formative answer review', () => {
  const definitions=tools().definitions({});
  assert.equal(definitions.length,5);
  const review=definitions.find((tool)=>tool.name==='review_current_answer');
  assert.ok(review);
  assert.deepEqual(Object.keys(review.inputSchema.properties),[]);
  assert.equal(review.inputSchema.additionalProperties,false);
  assert.equal(review.annotations.untrustedContentHint,true);
});

test('review tool returns feedback without committing work', async () => {
  const reviewTool=tools().definitions({reviewCurrentAnswer:()=>({activity_id:'fabric-choice',ready_to_submit:false,feedback:'Explain how stability supports the silhouette.',issues:['Add evidence.'],committed:false})}).find((tool)=>tool.name==='review_current_answer');
  const output=await reviewTool.execute({});
  assert.equal(output.structuredContent.committed,false);
  assert.match(output.content[0].text,/stability/);
});

test('progress tool reports actual completion and next step', async () => {
  const progressTool=tools().definitions({summary:()=>({percent:10,next_step:{label:'Complete practice 2.'}})}).find((tool)=>tool.name==='get_progress_and_next_step');
  const output=await progressTool.execute({});
  assert.match(output.content[0].text,/Complete practice 2/);
  assert.equal(output.structuredContent.percent,10);
});
