# Tone of voice

The tone controls voice and wording: sentence length, how direct the text is, how much it
explains and how warm it sounds. It never changes the conventions, the structure rules or
the process. A `tutorial` page still uses 2-space code indents and saves as a draft.

Always calibrate against two or three finished pages near the target. A preset describes a
direction, and the neighbouring pages show how far to go in it.

## Presets

### `automad` (default)

The voice of the finished docs pages, e.g. `/getting-started` and
`/developer-guide/themes/template-language/custom-functions`.

- Addresses the reader as "you". Calm, friendly and technical, never chatty or salesy.
- Opens with a short orienting paragraph: what it is, and when you would use it.
- Explains *why* or *when* before *how*, then shows a small concrete example and explains
  what it does.
- Plain, complete sentences of medium length. Light connectors such as "Alternatively",
  "In contrast to", "Note that".
- Contrasts the feature with related ones and links to them ("Custom functions vs.
  extensions").
- No marketing words ("powerful", "seamless", "effortless"), no exclamation marks, no emoji.

### `concise`

For reference pages: filters, functions, options and API-like lists.

- Lead with the fact. One or two sentences per point.
- Let tables and code carry most of the information. Prose only connects them.
- Leave out motivation unless a choice is non-obvious.

### `tutorial`

For getting-started flows and first-time tasks.

- A numbered `nestedList` for the steps, one action per step, with the expected result after
  steps that change something visible ("The dashboard now shows ...").
- Assumes less prior knowledge and briefly explains terms on first use, with a link.
- Ends with a short "Next steps" section that links onwards.

### `conversational`

For overview and landing pages.

- Warmer, may use "we" for the project ("we recommend ...").
- Slightly shorter paragraphs, the occasional rhetorical question is fine.
- Still precise: every claim must hold up against the code.

## Custom tone (free text)

When the user describes a tone in their own words (e.g. `tone: dry and very short, like a
man page`):

1. Turn the description into three to five concrete writing rules, such as sentence length,
   person, amount of explanation, formality and whether to use examples.
2. State those rules to the user in one short line before writing the pilot page, so a
   misreading is caught early.
3. Apply them on top of the conventions. Where the custom tone clashes with neighbouring
   finished pages, write in the requested tone anyway and mention the clash once in the
   report.

## Adding a preset

When the user likes a custom tone and wants to reuse it, add it here as a new `###` section
with a backticked name and three to six rules, following the presets above.
