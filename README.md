# Unikon WebMCP Fashion eSchool Demo

A standalone WordPress plugin demonstrating a human-first fashion lesson that exposes five structured tools to in-browser agents through the experimental WebMCP API.

Current plugin version: **0.1.1**.

## What the demo includes

- One original course, lesson, and deterministic fabric-choice exercise
- WordPress authentication and per-user progress
- A normal accessible interface that works without WebMCP
- Five WebMCP tools: learning state, lesson opening, exercise start, answer staging, and progress guidance
- A strict human confirmation gate: an agent can stage an answer, but only the learner can submit and save it

## Install

1. Copy the plugin folder to `wp-content/plugins/unikon-webmcp-demo`.
2. Activate **Unikon WebMCP Fashion eSchool Demo** in WordPress.
3. Open the generated **Fashion Learning Studio** page.
4. Sign in with any WordPress user account.

For a focused presentation, the optional companion theme is in `theme/unikon-webmcp-theme`. Activating it sets the generated learning page as the static homepage once and removes the duplicate outer page title from the front-page layout.

Activation creates the demo page once and reuses it on later activations. Deactivation preserves progress. Explicit uninstall removes this plugin's user metadata and removes the generated page only if its shortcode content was not edited.

## Try WebMCP

Use a current Chrome build with WebMCP enabled according to the [Chrome WebMCP documentation](https://developer.chrome.com/docs/ai/webmcp). Open DevTools → Application → WebMCP to inspect and manually invoke the five tools.

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

## Privacy and boundaries

The plugin stores only a small progress record in the signed-in user's WordPress metadata. It does not use an external AI API, store agent conversations, or include proprietary course content. See [docs/SECURITY.md](docs/SECURITY.md).

## License

GPL-2.0-or-later.
