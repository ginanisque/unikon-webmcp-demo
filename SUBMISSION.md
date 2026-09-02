# Devpost submission — Fashion Learning Studio

> Submission deadline: September 3, 2026 at 1:00 PM Pacific / 9:00 PM West Africa Time. Replace every `[TODO]` before submitting.

## Project name

Fashion Learning Studio

## Tagline

A human-first fashion eSchool where WebMCP agents guide learning, stage responses, and respect the learner's final say.

## Links

- Live app: https://webmcp.ginani.net/
- Public repository: https://github.com/ginanisque/unikon-webmcp-demo
- Public YouTube demo (under three minutes, with audio): `[TODO: YouTube URL]`

## Short description

Fashion Learning Studio is a standalone WordPress learning experience for fashion design and sewing. It combines accessible course pages, layered assessments, Vimeo-led practical lessons, WordPress authentication, and persistent learner progress with five browser-native WebMCP tools.

The agent can inspect the learner's current state, open the lesson, begin the next exercise, stage a proposed response in the visible form, and recommend the next step. The learner remains in control: the agent cannot claim a video was watched and cannot submit, grade, or save an answer. Only the learner's visible **Submit my answer** action commits work.

## Inspiration

Online learning systems usually make agents guess their way through page markup, while learners still have to navigate long courses and remember where they stopped. That is especially awkward in practical fashion education, where progress includes reading, watching demonstrations, reflecting, and practising physical techniques.

I wanted to explore a more trustworthy collaboration: an agent that understands the learning workflow through structured tools, helps the learner reach the right task, and drafts support directly in the human interface without taking credit for the learner's work.

## What it does

The demo contains three focused learning paths:

- **Fashion Foundations: Fabric to Silhouette** introduces fabric behaviour and an A-line skirt exercise.
- **Fashion Design Studio: Concept to Collection** uses six ordered layers spanning design signals, colour, silhouette, materials, mood-board editing, and a final rationale.
- **Sewing Skills: Machine Control to Finishing** provides 19 ordered Vimeo-led topics, short practice reflections, and a final essay.

Each signed-in learner has separate progress for every course. Passed layers unlock the next task. The site remains fully usable through its ordinary interface when WebMCP is unavailable.

## Why WebMCP is a strong fit

Learning is stateful and sequential. A general browser agent may see many buttons, hidden exercises, completed tasks, and video embeds without reliably knowing what is currently allowed. WebMCP gives the site an explicit vocabulary for the workflow and returns authoritative server-backed state.

This makes the experience more reliable than DOM guessing:

- the agent learns which actions are allowed before acting;
- tool schemas constrain activity and answer identifiers;
- prerequisite errors are returned in a structured form;
- actions visibly update the same interface the learner sees;
- the boundary between agent assistance and learner confirmation is explicit.

## What people and agents can do together

A learner can ask, “What should I learn next?” The agent reads course state rather than inferring it from layout, opens the right lesson, starts the available exercise, and can stage a concise proposed answer inside the unlocked assessment. The learner reviews or edits that work and personally submits it.

Without WebMCP, this requires the agent to repeatedly inspect and actuate a changing interface. With WebMCP, the site exposes the intended learning actions while the learner keeps control of consequential academic actions.

## WebMCP implementation

The browser registers five imperative tools with `document.modelContext.registerTool()` after feature detection:

1. `get_learning_state` — reads the signed-in learner's course, lesson, exercise, progress, and allowed actions.
2. `open_next_lesson` — opens the available lesson visibly and idempotently.
3. `start_exercise` — starts the current course exercise after prerequisites are met.
4. `stage_exercise_answer` — fills only the visible unlocked form for learner review; it cannot call the submission endpoint.
5. `get_progress_and_next_step` — returns completion and one recommended next action.

The tools use strict JSON Schemas, enums, length limits, `additionalProperties: false`, concise errors, `readOnlyHint` annotations, same-origin exposure, and lifecycle cleanup through `AbortController`. WordPress remains authoritative for authentication, state transitions, grading, and persistence. Every REST route requires a signed-in session and valid REST nonce.

## Human safety and privacy

- Agent-provided text is treated as untrusted plain text.
- The agent can stage but cannot submit learner work.
- Video completion is learner-confirmed.
- Progress belongs to the authenticated WordPress user.
- Answer text is not returned through the WebMCP state tools.
- Vimeo links are administrator-configured, allowlisted, and kept outside the public repository.
- Submission history is bounded to the 30 most recent attempts per course.

## How it was built

- WordPress plugin, PHP, the WordPress REST API, and user metadata
- Vanilla JavaScript and the imperative WebMCP API
- Accessible HTML/CSS with progressive enhancement
- Vimeo player embeds configured through a protected WordPress settings page
- A companion WordPress block theme focused on the learning experience
- Deterministic assessment rubrics and Node-based WebMCP/interface tests

No external AI API is required by the site itself; the browser agent supplies the intelligence and invokes the tools.

## Challenges

The hardest design problem was deciding where agent autonomy should stop. Automatically submitting an educational answer would make an impressive demo but undermine learner agency. The solution was to split assistance from commitment: the tool stages work in the ordinary form, displays a confirmation message, and leaves grading and persistence behind a learner-only button.

Another challenge was turning a conventional course backup into a public demonstration without exposing student records or proprietary implementation. The demo reimplements a small generic curriculum, stores Vimeo configuration separately, and excludes Moodle user profiles, submissions, feedback, and grading data.

## Accomplishments

- A coherent human interface that works with or without WebMCP
- Five task-specific tools connected to real authenticated learning state
- A visible, testable human-confirmation boundary
- Three distinct course structures, including layered essays and 19 video topics
- Private video configuration without private IDs in the public release
- Structured eval journeys plus deterministic client and server test coverage

## What I learned

WebMCP is most useful when it communicates application intent and trust boundaries, not merely when it replaces clicks. Tool names, schemas, state responses, and visible effects must agree. In an educational setting, preserving a meaningful learner action can be more valuable than maximizing automation.

## What's next

- Evaluate tool-selection accuracy with more natural-language learner prompts.
- Dynamically expose only tools relevant to the learner's current state.
- Add instructor-authored rubrics and feedback while preserving learner confirmation.
- Add accessible transcripts and captions alongside every practical video.
- Explore portable progress records across open learning platforms.

## Testing instructions for judges

### Browser setup

Use the ChatGPT desktop app's in-app browser, or Chrome 149 or later with `chrome://flags/#enable-webmcp-testing` enabled. Visit the live app directly so its tools can be discovered.

### Judge account

Judge credentials will be provided only through Devpost's private testing instructions and will not be stored in this public repository. The account should have the restricted WordPress Subscriber role.

### Suggested two-minute journey

1. Sign in and open https://webmcp.ginani.net/sewing-video-class/.
2. Ask: **“Show my learning state and tell me what I should do next.”**
3. Ask: **“Open my next lesson.”**
4. Ask: **“Start the exercise.”**
5. Ask the agent to stage a response for the visible unlocked activity.
6. Confirm that the form is filled but no submission was saved.
7. Review/edit the response and click **Submit my answer** yourself.
8. Ask: **“What is my progress and next step?”**

Expected result: tools navigate and stage visibly; only the human click grades and saves the response; the next activity unlocks after a passing answer.

### Alternative no-video journey

Use https://webmcp.ginani.net/ and complete the short Fashion Foundations fabric-choice flow.

## Demo video plan (target: 2:20)

- **0:00–0:15 — Problem:** Agents normally guess at complex learning interfaces; practical learning also needs a trustworthy human boundary.
- **0:15–0:30 — Product:** Show the three course tabs, visual lesson content, authentication, and saved progress.
- **0:30–0:45 — Discovery:** Open the WebMCP inspector and show the five registered tools.
- **0:45–1:10 — State and navigation:** Ask for learning state, open the next lesson, and start the exercise.
- **1:10–1:40 — Collaboration:** Ask the agent to stage an answer. Show the filled visible form and “Ready for your review” message.
- **1:40–1:58 — Human control:** Emphasize that nothing is saved, then personally click **Submit my answer** and show feedback/unlocking.
- **1:58–2:12 — Progress:** Ask for progress and the next step.
- **2:12–2:20 — Close:** “Structured tools for the agent; authorship and control for the learner.”

## Final submission checklist

- [ ] Confirm entrant eligibility and accept the official rules.
- [x] Confirm ownership/publication rights for every bundled course image; declaration recorded in `docs/COURSE-MEDIA.md`.
- [ ] Commit the complete 0.5.0 source and assets.
- [ ] Push to a public GitHub, GitLab, or Bitbucket repository.
- [ ] Make the GPL license visible at the repository root/About area.
- [ ] Verify the repository builds a functional plugin from its documented instructions.
- [ ] Create a fresh Subscriber judge account with no administrative access.
- [ ] Test every tool using that account in ChatGPT's in-app browser or Chrome 149+.
- [ ] Verify WebMCP in DevTools → Application → WebMCP.
- [ ] Confirm HTTPS, origin isolation, and the `tools` Permissions Policy.
- [ ] Confirm authenticated learning pages are not served from shared cache.
- [ ] Record and upload a public YouTube video under three minutes with audio.
- [ ] Add the public repository and YouTube links above.
- [ ] Put credentials only in Devpost's private testing instructions.
- [ ] Submit before 9:00 PM WAT on September 3, 2026.
- [ ] Freeze the submitted live site, repository, video, and Devpost entry throughout judging.

## Submission references

- [Devpost challenge overview and requirements](https://webmcp.devpost.com/)
- [Official challenge rules](https://webmcp.devpost.com/rules)
- [Official challenge resources and FAQ](https://webmcp.devpost.com/resources)
- [OpenAI WebMCP Challenge overview](https://openai.com/webmcp-challenge/)
- [Chrome WebMCP documentation](https://developer.chrome.com/docs/ai/webmcp)
- [Chrome WebMCP security guidance](https://developer.chrome.com/docs/ai/webmcp/secure-tools)
