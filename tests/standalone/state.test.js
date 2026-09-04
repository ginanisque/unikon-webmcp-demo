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

test('progress reflects actual lesson and assessment milestones', () => {
  const sewing={assessments:Array.from({length:19},(_,index)=>({id:`practice-${index+1}`,title:`Practice ${index+1}`}))};
  let value=state.openLesson(state.defaults());
  value=state.startExercise(value,'practice-1');
  assert.equal(state.progress(sewing,value).percent,5);
  value.activityStatuses['practice-1']='completed';
  value.activityStatuses['practice-2']='in_progress';
  const progress=state.progress(sewing,value);
  assert.equal(progress.percent,10);
  assert.match(progress.next_step.label,/Practice 2/);
});

test('formative review gives actionable feedback without changing state', () => {
  const assessment=course.assessments[0];
  const weak=state.review(assessment,'yes','Too short');
  assert.equal(weak.ready,false);
  assert.ok(weak.issues.length>0);
  const strong=state.review(assessment,'yes','A stable fabric supports the shape.');
  assert.equal(strong.ready,true);
});

test('normalization repairs malformed stored state', () => {
  assert.deepEqual(state.normalize({lessonStatus:'broken',activityStatuses:null,submissions:'bad'}),state.defaults());
});
