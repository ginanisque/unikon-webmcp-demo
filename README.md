# Fashion Learning Studio — Unikon WebMCP Demo

A human-first WordPress fashion eSchool where browser agents can navigate learning, stage responses, and recommend the next step through WebMCP—without submitting work on the learner's behalf.

[Live demo](https://webmcp.ginani.net/) · [Devpost submission draft](SUBMISSION.md) · [Security model](docs/SECURITY.md) · [Code audit](docs/CODE-AUDIT.md)

Current plugin version: **0.5.0**.

## What the demo includes

- Three compact courses with visual lesson content and deterministic exercises
- A six-layer Fashion Design assessment path with quizzes, applied responses, a final essay, retries, and saved submission history
- A 19-topic Vimeo-led Sewing Class with sequential practice responses and a final reflection essay
- WordPress authentication and per-user progress
- A normal accessible interface that works without WebMCP
- Five WebMCP tools: learning state, lesson opening, exercise start, answer staging, and progress guidance
- A strict human confirmation gate: an agent can stage an answer, but only the learner can submit and save it

## Why WebMCP

Course interfaces are stateful: some lessons are locked, some exercises are complete, and only one action may be appropriate next. WebMCP lets the site declare those actions instead of asking an agent to infer them from page layout. Every tool acts visibly in the learner's interface, while WordPress remains authoritative for identity, prerequisites, grading, and progress.

| Tool | Purpose |
| --- | --- |
| `get_learning_state` | Read current course state and allowed actions |
| `open_next_lesson` | Open the available lesson visibly |
| `start_exercise` | Begin the exercise after prerequisites are met |
| `stage_exercise_answer` | Fill an unlocked response for learner review—never submit it |
| `get_progress_and_next_step` | Return completion and one recommended next action |

## Install

1. Copy the plugin folder to `wp-content/plugins/unikon-webmcp-demo`.
2. Activate **Unikon WebMCP Fashion eSchool Demo** in WordPress.
3. Open the generated **Fashion Learning Studio** page.
4. Sign in with any WordPress user account.

Requirements: WordPress 6.4+, PHP 7.4+, HTTPS, and a WebMCP-capable browser for agent tools. The ordinary learning interface does not require WebMCP.

For a focused presentation, the optional companion theme is in `theme/unikon-webmcp-theme`. Activating it sets the generated learning page as the static homepage once and removes the duplicate outer page title from the front-page layout.

Activation creates the main learning page, Fashion Design Studio page, and Sewing Video Class page, then reuses them on later activations. Deactivation preserves progress. Explicit uninstall removes this plugin's user metadata and removes generated pages only when their shortcode content was not edited.

### Configure private Vimeo lessons

Open **Settings → Sewing Class Videos** and paste each Vimeo link, or paste the private JSON mapping into the bulk-import field. Links are validated, stored in the WordPress database, and never required in the public Git repository. A video topic cannot be submitted until its Vimeo URL is configured.

## Try WebMCP

Use ChatGPT's in-app browser or Chrome 149+ with `chrome://flags/#enable-webmcp-testing` enabled, according to the [Chrome WebMCP documentation](https://developer.chrome.com/docs/ai/webmcp). Open DevTools → Application → WebMCP to inspect and manually invoke the five tools.

Suggested journey:

1. “Show my learning state.”
2. “Open my next lesson.”
3. “I'm ready to practise fabric choice.”
4. Ask the agent to propose and stage an answer.
5. Review the visible form and click **Submit my answer** yourself.
6. “What is my progress and next step?”

## Development

JavaScript checks have no third-party dependencies:

```bash
npm test
npm run check
```

WordPress integration tests require the standard WordPress PHP test suite. See [docs/TESTING.md](docs/TESTING.md).

For the hackathon handoff, see [SUBMISSION.md](SUBMISSION.md) and the [code audit](docs/CODE-AUDIT.md).

## Privacy and boundaries

The plugin stores only a small progress record in the signed-in user's WordPress metadata. It does not use an external AI API or store agent conversations. Bundled course images were selected from course-level Moodle media; student submissions, profiles, feedback, and grading files were excluded. See [docs/SECURITY.md](docs/SECURITY.md) and [docs/COURSE-MEDIA.md](docs/COURSE-MEDIA.md).

## License

The plugin and companion theme source code are licensed under **GPL-2.0-or-later**. See [LICENSE](LICENSE).

Bundled course images are project-owner media and are not relicensed under the GPL. Their provenance and redistribution warning are documented in [docs/COURSE-MEDIA.md](docs/COURSE-MEDIA.md). Confirm publication rights before making the repository public; replace any image whose ownership cannot be established.
