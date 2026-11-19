---
name: automad-security-check
description: Find high and critical server-side security vulnerabilities in the Automad codebase — leaking or obtaining admin credentials, unauthenticated remote code execution or write access, leaks of other secrets (mail, AI provider, Composer, access tokens), site takeover by an unauthenticated attacker, and flaws in the setup/first-user flow and in any login or account-recovery flow — and write them into a dated report in .claude/security/ with a review-gated, step-by-step fix plan. Never fixes anything. Use this skill whenever the user asks for a security check, security review, security audit or vulnerability scan of this repo, or points to .claude/features/security-check-skill.md. Not for frontend code (automad/src/client) or npm packages.
argument-hint: "[focus area or path]"
---

# Automad Security Check

This skill finds high-impact and critical vulnerabilities in Automad's **server side** and
documents them in a report. It reports only. Fixing happens later, step by step, with a
review after every step, and only when the user asks for it.

## Hard limits

- **Read-only.** The only file this skill writes is the report, and only the main session
  writes it. Subagents never edit or write anything. No fixes, no commits, no changes to
  config, content or tests.
- **No secret values anywhere.** Never print or copy the contents of `config/*.php`, `.env`,
  key files, tokens or password hashes into the report or the chat. Name the file and the
  kind of secret only. (The sandbox blocks reading most of these files anyway. Don't try to
  get around that, since the code tells you everything you need.)
- **Live tests need permission.** Only send requests to the local dev instance
  (`npm run dev` or `npm run dev:docker`, `http://localhost:8000`), never to any other host,
  and only after the user agrees.
- **Out of scope:** `automad/src/client/**`, npm packages and `node_modules`. Third-party
  Composer packages (`vendor/`, `lib/vendor/`) count only when Automad passes visitor input
  to them.
- `.claude/.gitignore` keeps `.claude/security/` untracked. Leave it that way, because
  reports about unfixed vulnerabilities must not be committed.

## Workflow

### 0. Read the security model (mandatory, always first)

Before looking at any code, read **`SECURITY.md` at the repo root in full**, then the
"Security model" section of `.claude/CLAUDE.md`. Do this on every run, even if you think you
remember it. Everything below is judged against it:

- There are only two principals: **visitors** (no account) and **admins** (all equal, no
  roles, fully trusted: they can install Composer packages, change configuration, edit
  snippets and template code, and add JavaScript).
- **Access tokens** are admin credentials. Anything that needs a valid token needs admin
  access.
- A finding is a vulnerability **only if it crosses a boundary**, e.g. unauthenticated
  visitor → data or actions, visitor → admin, or a co-tenant on shared hosting → this
  install's files. Issues that need admin access are not vulnerabilities.
- Hidden pages and the public pagelist/search API are not a security boundary.
- Admin-authored content is rendered unescaped by design. XSS and CSRF only count as
  described in `SECURITY.md`.

### 1. Read prior reports

List `.claude/security/*-security-review.md` and open the newest one, if any. For each of
its findings, check the current code and set the new state:

- `Fixed` if the code now prevents it (say what changed),
- `Open` if it is still there (or `In progress` if a fix is partly in place),
- `Obsolete` if the affected code no longer exists.

Keep each finding's original index and title, and add new findings after them. Carry over
the "Checked and dismissed" items too, unless the code around them changed, in which case
check them again.

### 2. Map the unauthenticated attack surface

Derive the attack surface from the code on every run. Don't rely on this list, which only
names starting points (all paths are relative to `automad/src/server/` unless they say
otherwise):

- **`Routes.php`**: which routes are registered under which conditions
  (`$isAuthenticatedUser`, `$hasPendingTotpVerification`, `!$hasAccounts`,
  `Setup::isCompleted()`, `AM_PAGE_DASHBOARD`, `AM_MAINTENANCE_MODE_ENABLED`),
  `$publicApiRoutes`, `/_resize`, the feed, the MCP routes and the catch-all page render.
  Route order matters, because the first match wins (`Core/Router.php`).
- **`Api/RequestHandler.php`**: how the controller class and method are resolved from the
  URL, the `PUBLIC_CONTROLLERS` bypass of the POST and CSRF checks, `EditLock`, and
  `Request::mergeInputStream()`.
- **Public API controllers**: `Controllers/Api/PublicController`, and the methods behind the
  public routes in `SessionController`, `AppController`, `UserController` and
  `UserCollectionController`.
- **MCP**: `Controllers/McpController`, `Ai/Mcp/Provider::getTools()`, tools whose
  `requiresAuth()` returns `false`, `Auth/Token/AccessToken::verifyRequest()`, and the
  `.well-known` server card.
- **Page rendering**: `Controllers/PageController`, `Controllers/FeedController`, and
  anywhere in `Engine/**`, `Core/**` or `Models/**` where request data (query string, POST,
  cookies, headers, the request path) reaches file paths, `include`/`require`,
  `unserialize`, dynamic calls, cache keys or cache writes.
- **Web-server configs**: `.htaccess` at the repo root and the server configs under
  `docker/*`. Check what they deny (`config/`, `automad/`, `cache/`, `*.php`, dotfiles,
  `vendor/`, `lib/`) and what stays reachable on servers that ignore `.htaccess`.

Write the result down as a short list of entry points with their conditions. It goes into
every subagent prompt.

### 3. Fan out to three parallel subagents

Start three `general-purpose` subagents **in a single message** so they run in parallel. If
the user passed an argument that narrows the scope (a focus area or a path), start only the
agent or agents that cover it.

Every subagent prompt contains:

1. "Read `SECURITY.md` at the repo root in full before anything else." This applies to
   subagents too.
2. The trust model summary from step 0 and the entry-point list from step 2.
3. Its focus areas (below), with the file pointers.
4. Its rules: read-only, no edits or writes, no network requests, no secret values in the
   output, out of scope as listed under Hard limits.
5. The required output: a list of **candidates**, each with a title, `path:line`, a trace
   from the request to the sink, **who can reach it** (and under which conditions), a
   suggested severity, and a PoC sketch (the HTTP request). Also list what was checked and
   found safe, one line each, so that it can go into "Checked and dismissed".

| Agent | Focus areas |
| --- | --- |
| 1: Credentials and secrets | a, c |
| 2: RCE, write access and takeover | b, d |
| 3: Setup, login and recovery | e, f |

**a. Admin credentials**: `config/accounts.php` via `Models/UserCollection` and
`System/SerializedFile`; password hashing and verification in `Auth/**`; session handling,
fixation and ID regeneration (`Auth/Session/Session.php`); what `app/bootstrap`, `session/*`
and the public API return to anonymous clients; debug output (`Debug::getLog()` is attached
to API responses, so check when debugging is active and what it contains); files that are
servable over HTTP (`AM_DIR_CACHE` versus `FileSystem::getTmpDir()`/`AM_DIR_TMP`, backups,
temp files, logs) and the server-config deny rules from step 2.

**b. Unauthenticated RCE or write access**: anything that writes, moves or deletes files, or
includes or deserializes data, that a visitor can reach: path traversal in route, query or
POST parameters; `/_resize` and `Controllers/ImageController`; page and feed cache writes;
`unserialize` and `SerializedFile`; `include` of computed paths; regex anchoring in
`Core/Router.php`; the method name `RequestHandler` resolves from the URL (can a public route
reach a method it shouldn't?).

**c. Other secrets**: mail (`config.mail.php`, `System/Mail.php`), AI providers (`ai.*.php`,
`Ai/**`, `ProviderConfig`), Composer auth (`composer.auth.php`, `System/Composer/**`), access
tokens (`tokens.auth.php`, `Auth/Token/**`); the coverage of `System/Encryption` and
`System/Setup/Migrations/EncryptConfigFiles::PATTERNS`; leaks through error messages,
exceptions, logs, the MCP server card or API responses.

**d. Site takeover**: any path by which a visitor becomes an admin or replaces content,
templates, snippets, themes or packages: Composer and package-manager endpoints, update
endpoints, config writes, and MCP tools reachable without a valid token.

**e. Setup and first-user flow**: `UserCollectionController::createFirstUser` and the
`/create-user` routing, which are only open while `accounts.php` doesn't exist or can't be
read. Can a visitor cause that state (making the file unreadable, deleting it, a race on a
fresh install)? Can a visitor reach `System/Setup/**`, `SetupController` or the migrations
before authenticating? Check the `.setup-completed` marker file and how it is handled.

**f. Login and account recovery**: `SessionController` login, `Auth/Session/LoginRateLimiter`
(can it be bypassed by changing the username, the IP or headers?), TOTP
(`Auth/Session/Totp.php`, the pending-TOTP routes, brute force, code reuse),
`Auth/Session/PasswordResetCode` (entropy, expiry, binding to user and session, reuse,
timing, user enumeration), the invitation flow in `Auth/User.php`, and header injection in
`System/Mail.php`.

### 4. Check and merge (main session)

Subagent output is input, not a verdict. The main session checks every candidate itself:

1. Merge duplicates across agents.
2. Trace the exact path from the request to the sink in the code, and confirm that no earlier
   route condition, `RequestHandler` check or controller check blocks it.
3. Confirm who can reach it, per step 0. If it needs admin access or a valid access token, it
   is not a vulnerability.
4. Write a concrete PoC: the HTTP request or requests. If the local dev server is running,
   you may confirm it there, but only after the user agrees.

Candidates that don't hold up, need admin access, or rate below high go into
"Checked and dismissed", one line each with the reason. They get no fix steps.

### 5. Rate severity

- **Critical**: an unauthenticated visitor gets RCE, admin access or takeover of the site,
  or reads admin credentials or other secrets in plaintext, with no special preconditions.
- **High**: the same kind of impact with a realistic precondition (a specific web server or
  hosting setup, a race, one click by a logged-in admin), or unauthenticated writing or
  destruction of data short of takeover.
- Everything else is dismissed (step 4).

### 6. Write the report

Write the report to `.claude/security/<YYYY-MM-DD-HH-MM>-security-review.md`, taking the
timestamp from `date +%Y-%m-%d-%H-%M`. Create the directory if needed. Follow
`references/report-template.md` exactly.

Then reply in chat with a short summary only: the number of findings by severity and state,
and the report path. Don't paste the findings into the chat.

## Fix proposals

The fix plan in the report is for the user to work through later, one step at a time.

- **Lean fixes.** Make minimal changes: no extra helpers, logging, abstractions or test
  seams. Use plain, readable code, and reuse existing utilities such as
  `FileSystem::writePhpString()`/`readPhpString()`, `SerializedFile`,
  `FileSystem::getTmpDir()`, `Fetch`, `Session::verifyCsrfToken()` and
  `LoginRateLimiter`.
- **Respect the hosting constraints** in `.claude/CLAUDE.md`: no shell, `exec` or CLI access,
  no specific SAPI, non-public state goes in the tmp dir and never in `cache/`, and secrets go
  in `config/*.php` (encrypted when a key is set).
- **Don't break admin workflows** to harden against admins.
- **Small, ordered steps.** Order them by severity first, then by dependency. Each step
  fixes one finding, or a few closely related ones, and is small enough to review on its
  own. Every step ends with the review gate from the template.
- **Tests.** Each step names the PHPUnit tests to add or extend
  (`automad/tests/main/src/**`, see `.claude/docs/php.md` for the conventions), plus
  `npm run phpunit` and `npm run psalm`, and a manual check.
