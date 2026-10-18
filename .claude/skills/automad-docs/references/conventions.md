# Conventions for docs pages

These rules come from the user's reviews of earlier docs work. Where a rule has a reason,
the reason is given, so that new situations can be handled in the same spirit.

## Page structure

- Guide pages use the `page_sidebar_and_toc_centered` template. The page title is rendered as
  the h1 by the template, so content headings start at level 2 and never skip a level.
- Open with a short plain paragraph (one to three sentences) that tells the reader what the
  page covers and why it matters. Don't set `large: true` on any paragraph: the user doesn't
  want large lead paragraphs.
- A heading is never followed directly by another heading. Between an h2 and its first h3 or
  h4 (and between an h3 and its first h4), there is always at least one paragraph of real
  text that introduces the subsections. Stacked headings look broken on the rendered page.
  The same applies before a code block or table: lead into it with a sentence.
- Docs pages have no date. Create pages with `date: false`, and if a page you update has a
  date (the `date` field from `page_read` isn't empty), clear it by sending `date: false`.
- To list a page's subpages (overview pages), use a `table` block with `withHeadings: true`
  and two columns: the linked page name and a one-line description. The user prefers this to
  a `pagelist` block.
- Prefer real block types (`paragraph`, `header`, `nestedList`, `table`, `code`, `callout`)
  over `raw` HTML, so that the content stays editable in the dashboard.

## Inline formatting

- Use `<code>` for file names, option names, variables, CLI commands and routes such as
  `/dashboard`.
- Use `<i>` for UI labels and product or theme names, e.g. <i>Standard Lite</i>.
- Write internal links as absolute paths (`/developer-guide/...`). External links get
  `target="_blank"`.
- Link the first mention of a concept that has its own page.

## Code blocks

- Indent code with 2 spaces, never tabs or 4 spaces. This applies to every language.
- Take `language` from the enum in the `page_update` schema. Use `automad` for template
  language code, `php` for PHP (starting with `<?php`), `bash` for shell commands and `json`
  for JSON.
- Keep examples minimal and real. Take them from the codebase, the Standard Lite theme or the
  starter kit rather than inventing them.
- Don't escape HTML in `code` blocks. It is escaped automatically when rendered.

## Callouts

- Titles are short and consistent: **Note** for background, **Important** for things that
  break when ignored, **Tip** for optional shortcuts.
- Use a callout when two concepts are easy to mix up (e.g. theme components vs. the
  `component` block type), or for a requirement that must not be missed.
- Don't stack callouts. More than two or three per page dilutes them.

## Media

- Text only. The user adds images, videos and screenshots later.
- Keep existing `image`, `gallery`, `video` and `layoutSection` blocks (e.g. browser
  mockups) unchanged, including their ids and position, and write around them.

## MCP gotchas

- `page_update` replaces each content field you send as a whole. A block left out of the
  array is deleted. Always start from a `structured` read of the current page.
- Keep block ids stable for blocks that survive. New blocks have no id.
- Block data from your update is merged into existing blocks by id. To remove a flag such
  as `large`, send it explicitly as `false` (or drop the block id).
- Omitted top-level fields (`title`, `tags`, `template`, `theme`, `date`, `hidden`, `private`)
  keep their values. Leave them out unless the task is about them. The exception is `date`:
  send `date: false` whenever the page has one, since docs pages must not have a date. `tags` replaces the whole
  tag list, and existing tags should be reused (`tag_list`).
- `page_read` with `format: "text"` is for reading only. It can't be passed to `page_update`.
- Heading anchors are the slugified heading text: lowercase, with other characters replaced
  by dashes (e.g. "PHP's Built-in Web Server" → `#php-s-built-in-web-server`). Renaming a
  heading breaks links that point to its anchor, so search for them.
- `page_search` returns at most 50 results.
- Every write is saved as a draft. Never call `page_publish`.
