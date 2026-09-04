# Fashion Learning Studio — Unikon WebMCP Demo

A standalone fashion eSchool with guided lessons, practical assessments, saved browser progress, and formative feedback through WebMCP.

[Live demo](https://unikon-webmcp-demo.vercel.app/) · [Demo video](https://vimeo.com/1223916659) · [Course media](docs/COURSE-MEDIA.md)

Current website version: **0.6.2**.

## Demo login

- Username: `webmcp_judge`
- Password: `demo_judge`

## What the demo includes

- Three compact courses with visual lesson content and deterministic exercises
- A six-layer Fashion Foundations path from fabric testing and grain direction to construction planning and a final garment rationale
- A six-layer Fashion Design assessment path with quizzes, applied responses, a final essay, retries, and saved submission history
- A 19-topic Sewing Class with sequential practice responses and a final reflection essay
- Demo authentication and browser-local progress
- A normal accessible interface that works without WebMCP
- Five WebMCP tools: learning state, lesson opening, exercise start, answer review, and progress guidance
- A strict human confirmation gate: the learner writes and submits the answer while the learning assistant provides non-committing feedback

## Why WebMCP

Course interfaces are stateful: some lessons are locked, some exercises are complete, and only one action may be appropriate next. WebMCP lets the site declare those actions instead of asking an external assistant to infer them from page layout. Every tool acts visibly in the learner's interface, while the app remains authoritative for prerequisites, deterministic grading, and browser-local progress.

| Tool | Purpose |
| --- | --- |
| `get_learning_state` | Read current course state and allowed actions |
| `open_next_lesson` | Open the available lesson visibly |
| `start_exercise` | Begin the exercise after prerequisites are met |
| `review_current_answer` | Review the learner's visible response and explain what could be improved—never submit it |
| `get_progress_and_next_step` | Return completion and one recommended next action |

### WebMCP registration pattern

The production implementation defines all five tools in [`webmcp.js`](webmcp.js), feature-detects the browser API, awaits registration, and cleans registrations up with an `AbortController`.

```js
await document.modelContext.registerTool({
  name: 'get_learning_state',
  description: 'Read the signed-in learner’s current lesson, exercise state, progress, and allowed next actions.',
  inputSchema: {
    type: 'object',
    properties: {},
    additionalProperties: false,
  },
  annotations: { readOnlyHint: true },
  async execute() {
    return window.UnikonLearningApp.getState();
  },
});
```

The implementation wraps results in WebMCP structured content, reports tool-safe errors, and registers each definition through the detected `document.modelContext` instance.

## Run locally

```bash
npm run dev
```

The site has no runtime dependencies. Progress is stored in the signed-in browser's `localStorage`; demo login state lasts for the browser tab.

## Deploy

- **Vercel:** import the repository and deploy with the default settings. `vercel.json` builds and publishes `dist-site` with a single-page routing fallback.
- **Render:** create a Blueprint from the repository. `render.yaml` builds and publishes `dist-site` as a static site.

## Try WebMCP

Use ChatGPT's in-app browser or Chrome 149+ with `chrome://flags/#enable-webmcp-testing` enabled, according to the [Chrome WebMCP documentation](https://developer.chrome.com/docs/ai/webmcp). Open DevTools → Application → WebMCP to inspect and manually invoke the five tools.

Open the [Sewing Skills judge course](https://unikon-webmcp-demo.vercel.app/#course/sewing-video-class) directly. The `#course/sewing-video-class` hash is required because this is a single-page application.

Suggested journey:

1. “Show my learning state.”
2. “Open my next lesson.”
3. “Start the exercise.”
4. Write your own answer in the visible form.
5. “Review my current answer and advise me what to improve.”
6. Improve the response, then click **Submit my answer** yourself.
7. “What is my progress and next step?”

## Development

JavaScript checks have no third-party dependencies:

```bash
npm test
npm run check
```

## Privacy and boundaries

The website stores progress only in the learner's browser. It does not use an external AI API, database, or store agent conversations. The bundled demo credentials are intentionally public and must not be reused for a production authentication system.

## License

The website source code and retained legacy WordPress source are licensed under **GPL-2.0-or-later**. See [LICENSE](LICENSE).

Bundled course images are owned by Ginani and are not relicensed under the GPL. Their ownership declaration, provenance, and reuse terms are documented in [docs/COURSE-MEDIA.md](docs/COURSE-MEDIA.md).
