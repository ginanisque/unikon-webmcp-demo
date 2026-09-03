(function (global) {
  'use strict';

  function defaults() {
    return {lessonStatus:'not_started',exerciseStatus:'not_started',activityStatuses:{},submissions:[]};
  }

  function normalize(value) {
    const source=value && typeof value==='object' ? value : {};
    const valid=(status)=>['not_started','in_progress','completed'].includes(status);
    return {
      lessonStatus:valid(source.lessonStatus) ? source.lessonStatus : 'not_started',
      exerciseStatus:valid(source.exerciseStatus) ? source.exerciseStatus : 'not_started',
      activityStatuses:source.activityStatuses && typeof source.activityStatuses==='object' && !Array.isArray(source.activityStatuses) ? {...source.activityStatuses} : {},
      submissions:Array.isArray(source.submissions) ? source.submissions.slice(-30) : []
    };
  }

  function openLesson(state) {
    const next=normalize(state);
    if(next.lessonStatus==='not_started') next.lessonStatus='in_progress';
    return next;
  }

  function startExercise(state, firstActivityId) {
    const next=normalize(state);
    if(next.lessonStatus==='not_started') throw Object.assign(new Error('Open the lesson first.'),{code:'invalid_state'});
    if(next.exerciseStatus==='not_started') {
      next.exerciseStatus='in_progress';
      next.activityStatuses[firstActivityId]='in_progress';
    }
    return next;
  }

  function statusFor(state, assessmentId, index) {
    if(state.exerciseStatus==='not_started') return 'locked';
    return state.activityStatuses[assessmentId] || (index===0 ? 'in_progress' : 'locked');
  }

  function evaluate(assessment, answer, reason) {
    const response=String(reason||'').trim();
    const text=response.toLowerCase();
    const matches=assessment.keywords.filter((word)=>text.includes(word)).length;
    const passed=(assessment.correct===null || assessment.correct===answer) && response.length>=assessment.minLength && response.length<=assessment.maxLength && matches >= (assessment.type==='essay' ? 3 : 1);
    return {passed,feedback:passed ? 'This response meets the criteria. Continue to the next assessment.' : 'Review this layer and strengthen the response with specific evidence before trying again.'};
  }

  function submit(state, course, activityId, answer, reason, submittedAt) {
    const next=normalize(state);
    const index=course.assessments.findIndex((item)=>item.id===activityId);
    if(index<0 || statusFor(next,activityId,index)!=='in_progress') throw Object.assign(new Error('That assessment is not currently available.'),{code:'invalid_state'});
    const evaluation=evaluate(course.assessments[index],answer,reason);
    next.submissions.push({activityId,passed:evaluation.passed,submittedAt});
    next.submissions=next.submissions.slice(-30);
    if(evaluation.passed){
      next.activityStatuses[activityId]='completed';
      const following=course.assessments[index+1];
      if(following) next.activityStatuses[following.id]='in_progress';
      else {next.exerciseStatus='completed';next.lessonStatus='completed';}
    }
    return {state:next,evaluation};
  }

  const api={defaults,normalize,openLesson,startExercise,statusFor,evaluate,submit};
  global.UnikonState=api;
  if(typeof module!=='undefined'&&module.exports) module.exports=api;
}(typeof window!=='undefined' ? window : globalThis));
