# Code and submission audit

Audit date: September 2, 2026

Scope: WordPress plugin and companion theme source, REST routes, browser tools, progress storage, Vimeo configuration, tests, release package, repository state, and the public site's unauthenticated HTTP behavior.

## Outcome

No obvious critical injection, cross-user authorization bypass, arbitrary HTML/embed injection, private Vimeo mapping leak, or learner-record packaging issue was found in the reviewed code. The architecture has a strong human-confirmation boundary: the WebMCP staging tool has no submission request, while the server-side submission route requires authentication, a REST nonce, valid course state, constrained fields, and an unlocked activity.

The project is technically credible but is not submission-ready until the high-priority repository and media-rights items below are resolved.

## Findings

### Resolved — Public repository created and local remote configured

The public repository is https://github.com/ginanisque/unikon-webmcp-demo. Devpost requires it to contain all necessary source, assets, instructions, and a visible open-source license.

Remaining action: push the reviewed commit and test installation from a clean public clone.

### Resolved — Version 0.5.0 prepared for publication

The progress fix, course imagery, version bump, media documentation, and related changes were reviewed together for the public release.

Remaining action: verify the resulting public commit and test a clean clone before submission.

### High — Publication rights for course images require confirmation

Six images were selected only from course-level Moodle media; learner submissions, profile images, feedback, and grading files were excluded. However, the Moodle manifests label some selected media `allrightsreserved` and others `unknown`. At least one design visual contains metadata identifying AI-assisted generation.

The challenge requires the submission to be original, solely owned by the entrant, and non-infringing. The plugin's GPL license does not automatically grant rights to unrelated media.

Action: confirm that the entrant owns and may publicly redistribute every image. If that cannot be documented, replace the uncertain images with clearly original/openly licensed assets or remove them before submission. Keep accurate AI provenance metadata or disclosure where applicable.

### Medium — Judges require a restricted test account

WebMCP tools intentionally register only for authenticated learners. Signed-out REST access correctly returned HTTP 401 during the live check. Without credentials, judges cannot exercise the tools.

Action: create a dedicated WordPress Subscriber account, reset its course progress, test it, and put its credentials only in Devpost's private testing field.

### Medium — Production WebMCP headers need explicit verification

The live home-page response was HTTPS 200 but did not explicitly return `Origin-Agent-Cluster: ?1` or `Permissions-Policy: tools=(self)`. The `tools` policy defaults to `self`, and modern browsers may origin-key the document without an explicit header, but the submitted environment should be verified in the actual judging browser. The response also advertises SiteGround caching.

Action: verify `document.modelContext` and all five tools in DevTools on the live authenticated page. Prefer explicit `Origin-Agent-Cluster: ?1` and `Permissions-Policy: tools=(self)` headers, ensure no `Origin-Agent-Cluster: ?0`/`document.domain`, and confirm authenticated pages/REST responses bypass shared caching.

### Medium — Full browser-agent eval evidence is incomplete

The repository contains deterministic JavaScript tests, WordPress test cases, and a structured journey file. JavaScript tests pass locally. PHP/WordPress integration tests were authored but could not be executed in the current environment because PHP and the WordPress test suite are unavailable. No recorded ChatGPT in-app browser or Chrome 149 eval result is present.

Action: run the PHP suite in a WordPress test environment and record manual/agent results for tool selection, wrong-order calls, invalid parameters, retries, page refresh, and the complete learner-confirmation journey.

### Medium — WebMCP outputs can exceed the recommended character budget

State-changing tools return a full state payload containing all assessment summaries. The 19-topic sewing course can exceed Chrome's current recommendation of roughly 1,500 characters per individual tool output.

Action: after the submission freeze or before final testing if safe, consider returning a concise agent projection containing the current activity, allowed actions, progress, and next step while retaining full state internally for UI updates.

### Low — All five tools remain registered in every authenticated state

Server-side state enforcement prevents invalid transitions, and tool descriptions communicate prerequisites. Dynamically exposing only currently useful tools could further reduce wrong-order calls and strengthen WebMCP state leverage.

Action: treat dynamic registration as a future enhancement unless it can be thoroughly regression-tested before submission.

## Controls verified in source

- Strict tool schemas, enums, bounds, and rejection of extra parameters.
- `readOnlyHint` on read tools and no cross-origin `exposedTo` configuration.
- Tool-registration feature detection, awaited registrations, failure handling, and `AbortController` cleanup.
- WordPress cookie authentication plus REST nonce validation on every custom route.
- Server-derived user identity and course state; client claims are not authoritative.
- Server-side prerequisite enforcement and known activity allowlists.
- Plain-text escaping in rendered learner/course output.
- Vimeo hostname allowlist, numeric identifier extraction, optional private hash sanitization, and server-constructed player URLs.
- No raw Vimeo embed HTML or scripts accepted.
- Private Vimeo JSON and Moodle backups excluded from the release ZIP.
- Bounded per-course submission history and uninstall cleanup.
- Agent staging changes visible form fields only; a learner-triggered submit handler performs the sole grading request.
- Public interface remains operational without WebMCP.

## Verification performed

- `npm test`: passed, 2/2 tests.
- `npm run check`: passed JavaScript syntax checks.
- `git diff --check`: passed.
- Release `unikon-webmcp-demo-0.5.0.zip`: ZIP integrity passed; 23 entries; no Moodle backup, WebMCP resource ZIP, private Vimeo JSON, or private filename found.
- Live HTTPS home page: HTTP 200.
- Live unauthenticated custom REST state endpoint: HTTP 401 with the expected `authentication_required` response.
- Git history starts September 1, 2026, within the challenge submission period.

## Not verified

- Authenticated live REST behavior without a supplied test account/session.
- Actual tool discovery and execution in ChatGPT's in-app browser.
- PHP syntax and WordPress PHPUnit execution in this environment.
- SiteGround's cache behavior for authenticated HTML under a real WordPress session.
- Ownership and redistributability of bundled course images.
- Public repository completeness until the reviewed commit is pushed and tested from a clean clone.
