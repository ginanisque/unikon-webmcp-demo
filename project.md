# Unikon WebMCP Fashion eSchool Demo

## Status

Approved implementation plan. Codex may begin implementation in small, verified stages and commit each completed stage separately.

## Project goal

Build a standalone, open-source WordPress plugin that demonstrates an agent-assisted fashion eSchool using the experimental WebMCP browser API. The demo will include one original course, one lesson, one structured exercise, WordPress-authenticated learner progress, and five focused WebMCP tools.

The plugin must run independently of the proprietary Unikon eSchool plugin and the Ginani theme. It must remain useful as a normal human-operated WordPress experience when WebMCP is unavailable. On activation, it will create or reuse one clearly identified demo learning page so installation and judging require minimal setup.

## Workspace and reference boundaries

- Build and modify files only in this standalone repository.
- Treat the separately supplied proprietary eSchool project as a read-only architectural reference.
- Treat the separately supplied Ginani theme as a read-only visual-design reference.
- Do not modify, initialize Git in, or copy either proprietary reference project wholesale.
- Do not copy proprietary course content, student records, business logic, media, branding assets, or substantial source code.
- Reimplement only small, generic concepts required for this demonstration.

## Proposed user journey

1. A visitor opens the demo learning page.
2. WordPress asks unauthenticated visitors to sign in before accessing personal learning state.
3. A signed-in learner sees an original introductory fashion lesson and current progress.
4. The learner—or an in-browser agent acting visibly for the learner—opens the next lesson.
5. The learner starts a structured exercise about selecting appropriate fabric for a simple garment scenario.
6. The agent stages a proposed answer in a visible confirmation panel without changing server-side progress.
7. The learner reviews the staged answer and clicks **Submit my answer**.
8. WordPress evaluates the confirmed answer deterministically and saves progress against the current user.
9. The learner or agent requests progress and receives one clear next step.

All core actions will also have visible buttons and forms. WebMCP is a progressive enhancement, not the only interface.

## Original demonstration content

Proposed sample content, written specifically for this repository:

- Course: **Fashion Foundations: Fabric to Silhouette**
- Lesson: **Choosing Fabric for a First A-Line Skirt**
- Learning objective: identify how fabric weight, drape, and stability affect a simple A-line silhouette.
- Exercise: choose one fabric from a short fixed list and provide a brief reason using supplied criteria.
- Evaluation: deterministic rubric checks the selected fabric and required reasoning concepts; no external AI API is required.

The final copy will be original and intentionally small. It will not reproduce material from the existing Unikon installation.

## Proposed technical architecture

Use a minimal WordPress plugin with server-authoritative PHP and framework-free browser JavaScript. React and `use-webmcp-tool` are not proposed for the first version because five tools do not justify an additional application runtime or build pipeline. The direct imperative API also makes the demo easier to audit. This decision can be revisited before implementation.

```text
unikon-webmcp-demo/
├── project.md
├── README.md
├── LICENSE
├── unikon-webmcp-demo.php
├── uninstall.php
├── includes/
│   ├── class-plugin.php
│   ├── class-content.php
│   ├── class-progress.php
│   ├── class-rest-controller.php
│   └── class-assets.php
├── public/
│   ├── css/
│   │   └── learning-app.css
│   ├── js/
│   │   ├── learning-app.js
│   │   └── webmcp-tools.js
│   └── partials/
│       └── learning-app.php
├── tests/
│   ├── php/
│   ├── js/
│   └── evals/
│       └── journeys.json
└── docs/
    ├── SECURITY.md
    └── TESTING.md
```

Responsibilities:

- `class-content.php`: immutable sample course, lesson, exercise, and rubric definitions.
- `class-progress.php`: normalized per-user learning state and controlled transitions.
- `class-rest-controller.php`: authenticated REST endpoints shared by the human UI and WebMCP handlers.
- `class-assets.php`: loads styles/scripts only on the demo surface and passes the REST URL plus nonce.
- `class-plugin.php`: activates the plugin, creates or reuses the demo page, registers the shortcode, and coordinates services.
- `learning-app.js`: normal buttons/forms, UI rendering, live status, and focus management.
- `webmcp-tools.js`: feature detection, five tool definitions, awaited registration, AbortController lifecycle cleanup, request dispatch, and MCP result normalization.

## Data model

Store only the minimum learner state in one namespaced WordPress user-meta record, for example `unikon_webmcp_progress_v1`:

```json
{
  "version": 1,
  "lesson_status": "not_started|in_progress|completed",
  "exercise_status": "not_started|in_progress|completed",
  "attempt_count": 0,
  "selected_answer": null,
  "feedback_code": null,
  "updated_at": "ISO-8601 timestamp"
}
```

Do not store prompts, agent conversations, arbitrary HTML, sensitive profile fields, or data belonging to another user. Course content remains code-defined and versioned with the plugin.

## WebMCP integration

Use `document.modelContext.registerTool()` only after feature detection. Await every registration promise and handle registration failures without breaking the human interface. Pass an `AbortController` signal as the second registration argument and abort it when the page lifecycle ends. Do not expose tools to cross-origin sites.

Each handler calls the same authenticated REST application layer used by the visible interface. The browser is never trusted to determine user identity, completion status, scoring, or authorization.

### Tool contracts

#### `get_learning_state`

- Purpose: return the signed-in learner's current course, lesson, exercise, and availability state.
- Input: empty object.
- Mutation: none.
- Annotation: `readOnlyHint: true`.
- Output: compact state identifiers, human-readable labels, and allowed next actions.

#### `open_next_lesson`

- Purpose: open the next available lesson and mark it in progress when appropriate.
- Input: empty object.
- Mutation: may transition the lesson from `not_started` to `in_progress`.
- Annotation: `readOnlyHint: false`.
- UI effect: visibly navigates or focuses the lesson and announces the state change.
- Idempotency: repeated calls return the same active lesson without duplicating progress.

#### `start_exercise`

- Purpose: start the exercise after the lesson is available.
- Input: empty object.
- Mutation: may transition the exercise to `in_progress`.
- Annotation: `readOnlyHint: false`.
- UI effect: reveals/focuses the exercise and its answer controls.
- Guard: reject calls made before the prerequisite state is satisfied.

#### `stage_exercise_answer`

- Purpose: place one structured proposed answer into the visible exercise form for learner review.
- Input: a strict object containing a fixed answer identifier and a bounded plain-text reason.
- Server mutation: none. It must not record an attempt, grade the answer, or change progress.
- Annotation: `annotations: { readOnlyHint: false, untrustedContentHint: true }` because learner-controlled text enters the visible interface.
- UI effect: populate and focus an accessible confirmation panel, announce that submission is awaiting the learner, and stop.
- Human gate: only the learner's visible **Submit my answer** button sends the authenticated REST request that evaluates and commits the answer.
- Validation: enum allowlist, required properties, length limits, no additional properties.

#### `get_progress_and_next_step`

- Purpose: summarize completion and return exactly one recommended next step.
- Input: empty object.
- Mutation: none.
- Annotation: `readOnlyHint: true`.
- Output: percentage, completed milestones, and a single permitted next action.

### Tool-output rules

- Use concise MCP content plus small structured values where supported.
- Place `readOnlyHint` and `untrustedContentHint` in the registered tool's `annotations` object, never in its output.
- Keep descriptions under 500 characters, parameter descriptions under 150 characters, names under 30 characters, and each output under 1,500 characters.
- Return stable machine-readable error codes with short recovery guidance.
- Distinguish authentication, authorization, validation, invalid-state, conflict, and server failures.
- Never return stack traces, nonces, internal paths, private user data, or raw database errors.

## Security model

### Authentication and authorization

- Require an authenticated WordPress user for every learning-state endpoint and mutation.
- Derive learner identity exclusively from the server-side WordPress session.
- Use REST permission callbacks on every route; do not rely on hidden UI or JavaScript checks.
- Require a valid WordPress REST nonce for cookie-authenticated requests.
- Read and write only the current user's namespaced progress record.
- Apply least privilege; normal subscribers should not need administrative capabilities.

### Input, state, and output safety

- Define strict JSON Schemas with required fields, enums, length bounds, and `additionalProperties: false`.
- Sanitize incoming plain text, validate semantic state server-side, and escape all rendered output for its HTML context.
- Implement an explicit state machine so tools cannot skip prerequisites or invent progress.
- Make transitions idempotent where possible and reject stale or invalid transitions clearly.
- Use fixed rubric codes rather than executing or trusting learner-provided instructions.
- Treat learner text and externally sourced text as untrusted content and never interpret it as application instructions.
- Avoid logging answer text or personal information; log only minimal diagnostic codes in development.

### WebMCP exposure and browser policy

- Default to same-origin tool availability and do not set `exposedTo` without a separately approved need.
- Do not permit cross-origin iframe access.
- Document the `tools` Permissions Policy and origin-isolation requirements for deployment.
- Feature-detect the experimental API and degrade silently to the normal WordPress UI.
- Keep state-changing operations visible. The agent may stage an answer, but only an explicit learner click may evaluate and commit it.

### WordPress hardening

- Guard PHP entry points with `defined('ABSPATH') || exit`.
- Prefix or namespace all symbols, handles, routes, options, and metadata.
- Enqueue assets only where required.
- Use prepared queries if direct database access ever becomes necessary; prefer WordPress user-meta APIs.
- Include a narrowly scoped uninstall routine that removes only this plugin's metadata after an explicit uninstall policy decision.

## Visual design direction

Use the Ginani theme only as a visual guide. Recreate a small independent token set rather than importing theme files:

- Ink: `#101828`
- Muted text: `#475467`
- Brand navy: `#0B2A5B`
- Warm gold accent: `#C9A227`
- Surface: `#F8FAFC`
- Border: `#EAECF0`
- Body stack: Inter with system fallbacks
- Heading stack: Plus Jakarta Sans, then Inter/system fallbacks
- Wide content limit near 1120px and reading width near 760px
- Rounded cards, pill-shaped primary actions, clear focus rings, generous spacing, and restrained accent use

The plugin must not depend on those fonts being installed, and it will not copy logos, imagery, templates, CSS files, or branded copy. The interface should meet WCAG 2.2 AA basics: semantic landmarks, keyboard operation, visible focus, adequate contrast, associated labels, and polite live-region updates.

## Testing and evaluation plan

The hackathon build prioritizes one complete, reliable product journey over exhaustive test infrastructure.

### Required automated tests

- PHP tests for default state, permitted transitions, rejected prerequisite skips, deterministic scoring, and isolation between two users.
- REST tests for signed-out access, invalid authorization/nonce, malformed structured answers, and a successful learner-confirmed submission.
- JavaScript tests for WebMCP feature detection, exactly five registrations, schemas and annotations, AbortController cleanup, staged-answer behavior, and graceful registration failure.

### Required manual WebMCP evaluation

- Direct prompts: “Show my learning state,” “Open my next lesson,” and “Start my exercise.”
- Natural prompts: “What should I learn now?” and “I’m ready to practise fabric choice.”
- One negative prompt that must not trigger a mutation.
- Full journey: learning state → lesson → exercise → staged answer → learner confirmation → progress.
- Confirm that staging an answer does not alter server progress before the learner clicks **Submit my answer**.

### Required browser verification

- Enable the current WebMCP testing mechanism documented by Chrome.
- In DevTools, confirm exactly five correctly described tools.
- Manually invoke every tool once with valid input and verify visible UI synchronization.
- Test signed-out behavior, two user accounts, keyboard-only use, a narrow screen, and graceful operation in a browser without WebMCP.

### Deferred until after the hackathon

- Large prompt-selection datasets and exhaustive evaluation permutations.
- Every mid-chain network, cancellation, stale-state, and concurrency failure.
- Broad cross-browser matrices and full automated accessibility auditing.
- Exhaustive boundary and unit-test coverage beyond the critical security and product journey.

## Build checklist

Implementation may now begin from this approved checklist.

- [ ] Confirm plugin slug, display name, license, minimum WordPress version, and minimum PHP version.
- [ ] Create plugin bootstrap and namespaced loader.
- [ ] Add idempotent activation logic that creates or reuses one demo learning page.
- [ ] Write original course, lesson, exercise, and deterministic rubric.
- [ ] Implement default state and server-side transition service.
- [ ] Implement authenticated REST routes with permission callbacks and nonce validation.
- [ ] Build the accessible human learning interface.
- [ ] Add isolated visual tokens and responsive styles.
- [ ] Register the five WebMCP tools with strict schemas, correct annotations, awaited promises, and AbortController cleanup.
- [ ] Implement `stage_exercise_answer` so it changes only the visible form; require the learner's button click for REST evaluation and commitment.
- [ ] Add the focused PHP, REST, and JavaScript tests listed above.
- [ ] Run the single complete manual WebMCP journey and record reproducible testing instructions.
- [ ] Verify all five tools in Chrome DevTools.
- [ ] Test security boundaries, user isolation, graceful fallback, accessibility, and error recovery.
- [ ] Write installation, testing, security, privacy, and experimental-API documentation.
- [ ] Audit the repository for proprietary content, copied source, secrets, archives, and reference paths before publication.
- [ ] Package only the standalone plugin and produce a reproducible release ZIP.

## Approved implementation decisions

1. Use framework-free JavaScript rather than React for this demo.
2. Deliver the learning interface through a shortcode placed on an idempotently generated demo page.
3. Store the small progress record in namespaced WordPress user meta.
4. Use deterministic exercise feedback with no external model/API dependency.
5. License the standalone demo under GPL-2.0-or-later, consistent with WordPress ecosystem norms.
6. Remove plugin progress only on explicit uninstall, never on deactivation.
7. Keep final answer submission human-controlled: WebMCP stages; the learner confirms and commits.
8. Follow the current online WebMCP specification rather than bundling a downloaded specification archive.

## Explicitly out of scope for this iteration

- Importing or restoring complete Unikon or Moodle courses.
- Multiple courses or lessons.
- Photographic, video, CAD, or garment-work assessment.
- External AI APIs or AI-generated grading.
- Teacher dashboards, chat, certificates, payments, or enrolment commerce.
- Moodle integration and production Unikon integration.
- Publishing proprietary Ginani course content, business logic, student data, theme code, or paid assets.

## References

- [Chrome WebMCP overview](https://developer.chrome.com/docs/ai/webmcp)
- [Chrome WebMCP tool security](https://developer.chrome.com/docs/ai/webmcp/secure-tools)
- [Chrome WebMCP evaluations](https://developer.chrome.com/docs/ai/webmcp/evals)
- [Chrome DevTools WebMCP debugging](https://developer.chrome.com/docs/devtools/application/webmcp)
- [GoogleChromeLabs WebMCP demos](https://github.com/GoogleChromeLabs/webmcp-tools/tree/main/demos)
- [GoogleChromeLabs React hook](https://github.com/GoogleChromeLabs/use-webmcp-tool)
- [OpenAI Developers showcase: WebMCP apps](https://developers.openai.com/showcase?view=webmcp-apps)
