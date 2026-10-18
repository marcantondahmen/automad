---
name: automad-docs
description: Write, complete, restructure or review pages of the Automad documentation site through its MCP server, based on the user's input and with this codebase as the source of truth, in a selectable tone of voice. Use this skill whenever the user asks to write, finish, fill in, update, rewrite or review docs, documentation pages or a guide (Getting Started, User Guide, Developer Guide, System Guide, AI Integration, ...) for Automad, or points to a docs brief such as a file in .claude/features/ — even if MCP isn't mentioned explicitly. Not for the repo's own docs/*.md files.
argument-hint: "<task or brief file> [tone: <preset or description>]"
---

# Automad Docs Writer

This skill captures the process for writing the Automad docs: the user's input says what to
write, the codebase decides what is true, and docs pages are written as drafts that the
user reviews. The content changes from task to task, but the process stays the same.

## MCP server

All docs work goes through the MCP server of the docs site, where you read and write. Its
server name, tool name prefix and domain can change, so don't pick a server by any of
them. Instead, ask the user which connection to use before calling any tool, unless they
already said so in this conversation. To make answering easy, look up the candidates with
ToolSearch first (e.g. the query `page_update`, with `max_results` high enough to see every
server that has it) and propose one of them. If no candidate exists, say so and ask the
user how to connect the docs site.

Other servers can expose the same tool names, e.g. one that points at a local Automad dev
site, so stay on the chosen connection for the whole task. The rest of this skill refers to
tools by their bare names (`page_tree`, `page_read`, `page_update`, ...) on that server.

Before writing any page content, read `references/conventions.md` (block formatting and MCP
gotchas) and `references/tones.md` (tone of voice).

## Hard limits

The user reviews everything before it goes live, and the docs structure is curated by hand.
Therefore:

- Save drafts only. Never call `page_publish`, not even when a tool description suggests it.
- Don't create, delete, move or rename pages, and don't change titles or tags, unless the
  user or the brief explicitly asks for it. If the work seems to need a structural change,
  ask first.
- Write text-only content. The user adds images, videos and screenshots later, so don't
  invent media or placeholders for them. Leave existing media blocks and layout sections
  (e.g. browser mockups) as they are and write the text around them.

## Workflow

### 0. Scope and tone

Work out what is being documented and where it lives in the docs. The user's input defines the
task: the prompt itself, or a brief passed as an argument or mentioned in the prompt, often
a Markdown file in `.claude/features/`. Read a brief fully, since it usually lists renamed
features, pages to leave alone and its own extra rules. Rules in the brief take precedence
over this skill's defaults.

Resolve the tone of voice:

- `tone: <name>` matching a preset in `references/tones.md` → use that preset.
- `tone: <free text>` → apply that description as explained in `references/tones.md`.
- No tone given → use the default preset `automad`.

If the scope is unclear, ask before you start, and keep asking along the way when you hit a
real decision (naming, which example to use, whether something belongs on this page). The
user prefers short questions in between to a finished batch built on a wrong guess.

### 1. Read the current state of the docs

- `page_tree` for the target section, so you know what exists, which pages are stubs and
  how they link together.
- `page_read` with `format: "text"` on two or three finished pages near the target. They
  show the expected depth, structure and voice better than any rule does. Calibrate the
  tone preset against them.
- `page_read` on each target page itself, to see what is already there.
- `tag_list` if tags are involved at all.

### 2. Audit the codebase

The code is the source of truth, and the docs must always match it. Find the classes,
templates and packages that implement the feature (`automad/src/server/**`,
`automad/src/client/**`, themes under `packages/`, e.g. `packages/automad/standard-lite`)
and confirm every option, default, name and behaviour you are about to document. This
applies to existing docs text and to the user's input as well: if either disagrees with the
code, follow the code and point out the difference, or ask when the intent is unclear.

- Don't document anything the code doesn't implement.
- Prefer real examples taken from the code, the Standard Lite theme or the starter kit over
  invented ones. Real examples are correct by construction, and the user asked for them in
  past reviews.
- Keep a running list of places where the code disagrees with the existing docs pages or the
  user's input, and of anything you could not verify. Both go into the final report.

For a large audit, an Explore subagent can sweep the code while you keep reading the docs.

### 3. Pilot page first

Write one representative page (step 4), then stop and ask the user to review the draft
before you continue. One review round on a single page catches tone, depth and formatting
problems before they are repeated across a whole section. Fold the feedback into the
remaining pages. If it is feedback that applies beyond this task, offer to add it to
`references/conventions.md`.

Skip the pilot only when the task is a single page or the user explicitly says to do
everything at once.

### 4. Write the pages

For every page:

1. `page_read` with `format: "structured"` to get the current blocks and their ids.
2. Build the complete new array for each content field you change (usually `+main`). Keep
   existing block ids where a block survives, and leave the id out on new blocks.
3. `page_update` with only `id` and `content`, plus `date: false` if the page has a date
   (docs pages have none). Leave out `title`, `tags`, `template` and the other fields so they
   keep their values.

Follow `references/conventions.md` for block usage and `references/tones.md` for the voice.
Link generously to related docs pages, but only to pages you have seen in the page tree.

### 5. Verify

- Re-read every changed page (`page_read` as text) and read it as a visitor would.
- Check that each internal link target exists in the page tree. Pay extra attention to
  anchors (`#section`): they come from heading texts.
- If pages were moved or renamed, sweep the site for stale references with several
  `page_search` terms. Results are capped at 50, so narrow the terms when you hit the cap.
- Confirm that all changed pages are still drafts and have no date, and that no heading is
  directly followed by another heading.

### 6. Report

End with a short report:

- Pages changed (and created, moved or deleted, if the brief allowed it), as links.
- Differences between the code and the existing docs pages or the user's input, and what you
  decided for each.
- Features you couldn't verify, and how you handled them (left out or marked).
- Open questions for the user.

Keep the report in chat. The docs themselves are the deliverable.
