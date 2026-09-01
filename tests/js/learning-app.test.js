const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

test('uses DOM REST configuration when WordPress inline localization is absent', async () => {
  let requestedUrl;
  let requestedNonce;
  let requestedCourse;
  const root = {
    dataset: {
      authenticated: 'true',
      restRoot: 'https://example.test/wp-json/unikon-webmcp-demo/v1/',
      restNonce: 'dom-nonce',
      courseId: 'fashion-design-studio',
    },
    querySelector() { return null; },
    addEventListener() {},
    dispatchEvent() {},
  };
  const document = { querySelector(selector) { return selector === '[data-uwmcp-app]' ? root : null; } };
  const window = { setTimeout, UnikonWebMCPDemo: undefined };
  const fetch = async (url, options) => {
    requestedUrl = url;
    requestedNonce = options.headers['X-WP-Nonce'];
    requestedCourse = options.headers['X-Unikon-Course'];
    return { ok: true, async json() { return { progress: { percent: 0, next_step: { label: 'Open lesson' } } }; } };
  };
  const context = vm.createContext({ console, CustomEvent: class {}, document, fetch, FormData: class {}, setTimeout, window });
  const source = fs.readFileSync(path.join(__dirname, '../../public/js/learning-app.js'), 'utf8');
  vm.runInContext(source, context);

  assert.ok(window.UnikonLearningApp, 'learning app should initialize');
  await window.UnikonLearningApp.request('state');
  assert.equal(requestedUrl, 'https://example.test/wp-json/unikon-webmcp-demo/v1/state');
  assert.equal(requestedNonce, 'dom-nonce');
  assert.equal(requestedCourse, 'fashion-design-studio');
});
