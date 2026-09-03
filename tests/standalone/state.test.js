const test = require('node:test');
const assert = require('node:assert/strict');
const state = require('../../state.js');

const course = {assessments:[
  {id:'first',type:'choice',correct:'yes',keywords:['stable'],minLength:12,maxLength:100,choices:{yes:'Yes'}},
  {id:'second',type:'short_answer',correct:null,keywords:['shape'],minLength:12,maxLength:100,choices:{}}
]};

test('lesson and exercise transitions are idempotent', () => {
  let value=state.openLesson(state.defaults());
  value=state.startExercise(value,'first');
  value.activityStatuses.first='completed'; value.exerciseStatus='completed'; value.lessonStatus='completed';
  assert.equal(state.openLesson(value).lessonStatus,'completed');
  assert.equal(state.startExercise(value,'first').exerciseStatus,'completed');
});

test('locked assessments cannot be submitted', () => {
  const value=state.startExercise(state.openLesson(state.defaults()),'first');
  assert.throws(()=>state.submit(value,course,'second','','A shape-based answer.',new Date().toISOString()),/not currently available/);
});

test('passing work unlocks the next assessment and completion is bounded', () => {
  let value=state.startExercise(state.openLesson(state.defaults()),'first');
  let result=state.submit(value,course,'first','yes','A stable choice.',new Date().toISOString());
  assert.equal(result.state.activityStatuses.second,'in_progress');
  result=state.submit(result.state,course,'second','','The shape is controlled.',new Date().toISOString());
  assert.equal(result.state.exerciseStatus,'completed');
  assert.equal(result.state.lessonStatus,'completed');
});

test('normalization repairs malformed stored state', () => {
  assert.deepEqual(state.normalize({lessonStatus:'broken',activityStatuses:null,submissions:'bad'}),state.defaults());
});
