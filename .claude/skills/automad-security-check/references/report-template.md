# Report template

Use this structure for every report. Leave out the placeholder comments, keep the headings.
Write plain, direct English: short sentences, and no secret values anywhere.

````markdown
# Security Review YYYY-MM-DD HH:MM

- Scope: server side (`automad/src/server`, entry points, web-server configs). Focus: <all | argument>.
- Commit: `<git rev-parse --short HEAD>` on `<branch>` (plus uncommitted changes, if any).
- Previous report: `<path>` or none.
- Trust model: `SECURITY.md`. Admins and access tokens are fully trusted, so only findings that cross a boundary are listed.

## Findings

| # | Title | Description | Severity | Reachable by | State | Step |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | <short title> | <one sentence> | Critical | Unauthenticated visitor | Open | 1 |

States: `Open` · `In progress` · `Fixed (pending review)` · `Fixed` · `Obsolete`

<!-- If there are no findings, say so in one line instead of the table. -->

## Checked and dismissed

- <area or `path`>: <what was checked>. <Reason: requires admin / requires access token / below high / blocked by `path:line`>.

## Finding 1: <title>

- **Severity:** Critical | High
- **Reachable by:** <who, under which conditions>
- **State:** Open (<what changed since the previous report, if anything>)
- **Location:** `automad/src/server/...:<line>`

### Description

<What is wrong, the trace from the request to the sink, and which boundary it crosses.>

### Proof of concept

```http
POST /_api/... HTTP/1.1
Host: localhost:8000
...
```

<What happens and how to tell that it worked.>

### Impact

<The concrete result for the site owner.>

### Proposed fix

<An overview. The details are in Step N.>

## Fix plan

Work through the steps in order. After each step, stop for a review before starting the next.

### Step 1: <title> (fixes #1)

- **Files:** `...`
- **Change:**

  ```php
  // minimal sketch
  ```

- **Keep in mind:** <side effects, admin workflows that must keep working, hosting constraints>
- **Tests:** <PHPUnit test to add or extend>, then `npm run phpunit` and `npm run psalm`.
- **Manual check:** <steps on the local dev site>

> ⏸ **Review:** stop here until the user has reviewed and approved this step.
````
