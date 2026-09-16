# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Automad is a flat-file content management system and template engine, written in PHP (backend) with a TypeScript/JS dashboard (frontend). It is a single-package repo (no lerna/nx/turborepo, no npm workspaces) — one root `package.json` and one root `composer.json`.

Note: per the project README, **pull requests to this repository are not accepted** (a GitHub Action auto-closes them). This is a solo-maintained project; treat that as background context, not a constraint on local work in this repo.

## Common commands

Install: `composer install` and `npm install`. Composer packages in `lib/` (Automad's own runtime deps, declared in `lib/composer.json`) must also be installed via `composer install --working-dir=lib`.

- `npm run dev` — local dev: starts the PHP built-in server (`bin/server.sh`, `0.0.0.0:8000`) and esbuild in watch mode (`bin/dev.sh`).
- `npm run dev:docker` — same, but serves PHP via Docker (pick a flavor — apache/caddy/frankenphp/litespeed/nginx — under `docker/`) instead of the PHP built-in server.
- `npm run build` — production build: `bin/prebuild.sh` (clears `automad/dist/build`, copies Prism/font assets) then `node esbuild.js` (minified).
- `npm test` — runs everything: `phpunit && psalm && typecheck && vitest` (in that order).
- `npm run phpunit` — runs `bin/phpunit.sh`, which runs both PHPUnit suites: `./phpunit-12.5.4.phar` (main, config `phpunit.xml`) then `./phpunit-12.5.4.phar -c phpunit-i18n.xml` (i18n). The phar is auto-downloaded if missing. Main tests live in `automad/tests/main/src/**/*Test.php`; i18n tests in `automad/tests/i18n/src/**/*Test.php`.
  - Single test: `./phpunit-12.5.4.phar --filter testMethodName` or `./phpunit-12.5.4.phar automad/tests/main/src/Blocks/VideoTest.php`.
- `npm run psalm` — static analysis via vendored `psalm-6.14.3.phar` (`bin/psalm.sh`), scoped to `automad/src/server` only (see `psalm.xml`, errorLevel 3).
- `npm run typecheck` (or `typecheck:watch`) — `tsc --noEmit` against `tsconfig.json`.
- `npm run vitest` (or `vitest:watch`) — JS/TS unit tests via Vitest (`vitest.config.js`, root `automad/src/client/admin`, jsdom env). Test files: `automad/src/client/admin/tests/*.test.ts`.
  - Single test file: `npx vitest run automad/src/client/admin/tests/form.test.ts` (add `-t "test name"` to filter by name).
- There is no ESLint/Stylelint config in this repo. Prettier config is inlined in `package.json` (`trailingComma: es5`) but no script invokes it — use `npx prettier --write <path>` if needed. `.editorconfig` at the root defines tabs/single-quotes/size-4 conventions (JSON files use spaces/size 2).
- `npm run unused` — finds unused code/assets (`bin/find-unused.sh`).

CI (`.github/workflows/`) does not run lint/build/test on PRs (consistent with the no-PRs policy) — it only builds/publishes dist archives, Docker images, and release notes on tags/manual dispatch.

## Code conventions

Every new PHP file (`automad/src/server/**`) and every new TS file (`automad/src/client/**`) must start with the project's standard file header — the ASCII-art Automad logo followed by a copyright/license comment block. Copy the exact header from an existing file rather than retyping it, e.g. `automad/src/server/App.php` for PHP or `automad/src/client/admin/index.ts` for TS. For PHP files the header comment comes after the opening `<?php` tag; for TS files it is the first thing in the file. Keep the copyright year range's end year current.

## Architecture

### Backend (PHP, `automad/src/server/`)

- **Entry point**: `index.php` → `automad/init.php` → `automad/src/server/App.php`, which instantiates `new Automad\App()`. `App` runs env checks, sets up autoloading/config/session, then dispatches: `$Router = new Router(); Routes::init($Router); $callable = $Router->get(AM_REQUEST); return $callable();`. There is no MVC framework — routing is a plain regex/route-table dispatcher.
- **Autoloading is custom, not Composer PSR-4**: `automad/src/server/Autoload.php` maps `Automad\` directly onto `automad/src/server/` (e.g. `Automad\Models\Page` → `automad/src/server/Models/Page.php`). Composer's autoloader (`vendor/autoload.php`, plus a second one at `lib/vendor/autoload.php` for Automad's own runtime deps declared in `lib/composer.json`) only covers third-party packages.
- **Routes** are registered in `automad/src/server/Routes.php` (dashboard, JSON API, RSS feed, image resize, catch-all page render), with auth gating done inline via booleans passed as each route's "enabled" condition.
- Key subdirectories under `automad/src/server/`:
  - `Core/` — framework internals (`Automad.php` central context object, `Router.php`, `Request.php`, `Config.php`, `Cache.php`, `Blocks.php`, `Feed.php`).
  - `Models/` — domain models (`Page.php`, `PageCollection.php`, `Shared.php` for site-wide data, `ComponentCollection.php`, `UserCollection.php`).
  - `Stores/` — persistence layer that reads/writes the flat-file JSON content.
  - `Controllers/` and `Controllers/API/` — page-render controller and JSON API controllers, used by both `Routes.php` and the dashboard's API request handler.
  - `Engine/` — template engine that renders theme templates (`Document/`, `Globals/`, `Processors/`, `Toolbox.php`).
  - `Admin/` — server-side dashboard rendering (`Dashboard.php`, `State.php`) and transactional email.
  - `Auth/`, `System/` (installer, Composer integration, AI providers, image processors, mail), `Console/` (CLI, invoked via `automad/console`), `Blocks/` (Editor.js-style block rendering, shared by theme templates and reusable components).
- **Content is flat-file**, stored as JSON/Markdown under `pages/` and `shared/` at the repo root — this is CMS _content_ for the local dev site instance, not application source. `shared/components` is one such data file: named, reusable Editor.js block groups, backed by `Stores/ComponentStore.php` → `Models/ComponentCollection.php` → `Controllers/API/ComponentController.php`. It changes whenever someone edits components through the running dashboard — treat changes there as runtime state, not code.
- Themes/extensions (e.g. the default `standard-lite` theme) are separate Composer packages installed under `packages/` via `automad/package-installer`, not part of the npm build.

### Frontend (TS/JS dashboard, `automad/src/client/`)

- `admin/` — the dashboard app: `components/` (Breadcrumbs, Fields, Forms, Modal, Sidebar, Pages, System, PackageManager, …), `core/` (bootstrap, router, state, API request client, tooltips, undo), `editor/` (Editor.js integration: `blocks/`, `inline/`, `plugins/`, `tunes/`), `styles/` (Less, mirrors component structure), `tests/`.
- `common/` — shared between the dashboard and public-facing site (routes, request, sections, types, SVG icons).
- `blocks/`, `inpage/`, `consent/`, `mail/`, `prism/` — other client bundles (public-page block rendering, in-page editing widgets, cookie consent, email templates, syntax-highlighting theme).
- **No component framework/library** (no Lit/Stencil/React) — a custom `BaseComponent` (`automad/src/client/admin/components/Base.ts`, `abstract class extends HTMLElement`) is the base for ~89 native custom elements, registered via a `define`/factory helper (`core/factory.ts`).
- **Bundler is esbuild** (`esbuild.js` at repo root). Its header comment documents the entry-point convention: `index.ts` files under `automad/src/client/**` are main entry points loaded by PHP pages; files matching `blocks/components/*.ts` are split as dynamically-imported block classes; files matching `vendor/*.ts` are split as separately-hashed vendor chunks. Styles compile through the same esbuild pipeline (`esbuild-sass-plugin`, Less, PostCSS/autoprefixer). Output goes to `automad/dist/build`.
- **Path alias**: `@/*` → `automad/src/client/*` (`tsconfig.json`; target `es2022`, module `esnext`, `moduleResolution: bundler`, `noImplicitAny: true`).

## Hosting constraints

Automad targets mainstream, budget-friendly shared hosting with a minimal, restricted PHP feature set — this is a hard project requirement, not a nice-to-have. When writing or reviewing server-side PHP:

- Don't assume shell/CLI access or `exec`/`proc_open`/`shell_exec`-family functions are available at runtime — the project's own dev tooling reflects this by vendoring `phpunit`/`psalm` as standalone phars instead of requiring global Composer dev installs.
- Outbound HTTP requests should go through `Automad\System\Fetch`'s cURL wrapper, which defensively checks `curl_init()` for availability rather than assuming the extension is present.
- Don't assume a specific PHP SAPI — mod_php, PHP-FPM/FastCGI, CGI, LiteSpeed, and FrankenPHP must all work (see the `docker/` folder's multiple server flavors).
- The MCP integration authenticates with a plain static Bearer token rather than OAuth — no authorize/token endpoints, no discovery metadata. Token-based auth itself lives in `Automad\Auth\AccessToken` (issuance/verification) and `Automad\Auth\AccessTokenConfig` (persistence), deliberately named generically rather than after MCP, since the same primitive is meant to be reusable for other future auth use cases — MCP is just its first consumer. Tokens are issued through the dashboard (`Controllers/API/AccessTokenController::addToken()`, which delegates to `AccessToken::issue()`; shown once, only a hash is persisted) and sent by the MCP client as a static `Authorization: Bearer <token>` header, the same pattern GitHub's MCP server and most personal-access-token-based integrations use. `McpController::render()` verifies incoming requests via `AccessToken::verifyRequest()` rather than doing bearer-token parsing/hashing itself. This was chosen for simplicity (a well-known, minimal auth pattern) — don't reintroduce OAuth for this endpoint without a real reason to.
