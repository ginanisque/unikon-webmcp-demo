# Security and privacy

## Trust boundaries

The WordPress server is authoritative for identity, state transitions, attempts, and grading. Browser state and agent arguments are untrusted. Every REST route requires a signed-in WordPress session and a valid REST nonce; the server derives the learner ID from that session.

Tools are registered only on the signed-in learning page, use same-origin defaults, and do not set `exposedTo`. Deployment must preserve origin isolation and the default `tools` Permissions Policy. Cross-origin frames should not receive tool access.

## Human confirmation

`stage_exercise_answer` only fills the visible form. It has no code path to the submission endpoint. Grading and persistence happen only in the form's learner-triggered submit handler. The staged reason is marked as untrusted content and is treated only as plain text.

## Data stored

The plugin stores lesson and activity status, bounded submission history, answer text, attempt number, feedback code, and submission time under a separate namespaced user-meta key for each demo course. Answer text is private to the authenticated learner and is never returned by WebMCP state tools. At most 30 recent submissions are retained per course. The plugin does not store agent conversations, arbitrary HTML, or external profile data.

Deactivation retains progress. Explicit uninstall removes the namespaced user metadata. It deletes the generated page only if the page still contains exactly the plugin shortcode, preserving a page that an administrator has edited.

## Reporting

Do not include WordPress nonces, cookies, user records, server paths, or answer text in a public issue. Report a vulnerability privately to the repository maintainer.
