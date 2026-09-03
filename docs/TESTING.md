# Testing

> Legacy reference: the WordPress/PHP sections below apply only to the retained plugin source. Current standalone checks are run with `npm test`, `npm run check`, and `npm run build` from the repository root.

## JavaScript

Run `npm test` and `npm run check`. The tests verify exactly five registrations, strict schemas, annotations, awaited lifecycle registration, abort cleanup, graceful failure, and the non-committing staged-answer path.

## WordPress PHP and REST

Install the standard WordPress PHPUnit test library and set `WP_TESTS_DIR`, then load `tests/php/bootstrap.php` with your PHPUnit configuration. The focused tests cover default state, prerequisite enforcement, deterministic scoring, two-user isolation, signed-out access, invalid nonce handling, malformed answers, and successful confirmed submission.

## Manual browser journey

1. Enable WebMCP using the current Chrome instructions.
2. Sign in and open Fashion Learning Studio.
3. In DevTools → Application → WebMCP, confirm exactly five tools.
4. Invoke every tool once and check its visible UI effect.
5. Stage an answer and verify progress and attempt count do not change.
6. Click **Submit my answer** and verify that only this click grades and persists it.
7. Complete each Fashion Design layer in order, retry one failed response, and verify the final essay unlocks only after the earlier layers pass.
8. Repeat with a second WordPress user and confirm isolated progress.
9. Repeat signed out, with keyboard navigation, at narrow width, and in a browser without WebMCP.

For Sewing Class, verify that an unconfigured video blocks its response form, all 19 configured Vimeo players use `player.vimeo.com`, and passing one response unlocks only the next topic.

The prompt expectations and ordered journey are recorded in `tests/evals/journeys.json`.
