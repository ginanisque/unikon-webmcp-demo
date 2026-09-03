const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function curriculum() {
  const context = {window: {}};
  vm.runInNewContext(fs.readFileSync('curriculum.js', 'utf8'), context);
  return context.window.UnikonCurriculum;
}

test('provides all three standalone courses', () => {
  assert.deepEqual(Object.keys(curriculum()), ['fashion-foundations', 'fashion-design-studio', 'sewing-video-class']);
});

test('every assessment has deterministic evaluation fields', () => {
  for (const course of Object.values(curriculum())) {
    assert.ok(course.assessments.length > 0);
    for (const item of course.assessments) {
      assert.ok(item.id && item.title && item.prompt);
      assert.ok(item.minLength >= 12 && item.maxLength <= 1200);
      assert.ok(item.keywords.length > 0);
    }
  }
});

test('Fashion Foundations is a complete six-layer course', () => {
  const course = curriculum()['fashion-foundations'];
  assert.equal(course.assessments.length, 6);
  assert.deepEqual(
    Array.from(course.assessments, (item) => item.id),
    ['fabric-choice', 'fabric-behaviour', 'grain-direction', 'silhouette-match', 'construction-plan', 'foundation-rationale']
  );
  assert.equal(course.assessments.at(-1).type, 'essay');
});

test('Sewing course is self-contained without external videos', () => {
  const course = curriculum()['sewing-video-class'];
  assert.equal(course.assessments.length, 19);
  for (const assessment of course.assessments) {
    assert.ok(assessment.content.length > 80);
    assert.doesNotMatch(assessment.title + assessment.prompt, /video|after watching/i);
  }
});

test('deployment entrypoint loads standalone assets', () => {
  const html = fs.readFileSync('index.html', 'utf8');
  for (const asset of ['curriculum.js', 'state.js', 'app.js', 'webmcp.js', 'site.css']) assert.match(html, new RegExp(asset.replace('.', '\\.')));
  assert.doesNotMatch(html, /\.php|wp-content|WordPress/i);
  assert.match(html, /webmcp_judge/);
  assert.match(html, /demo_judge/);
});

test('production build excludes legacy executable source', () => {
  for (const path of ['dist-site/public/partials', 'dist-site/public/js']) assert.equal(fs.existsSync(path), false);
});
