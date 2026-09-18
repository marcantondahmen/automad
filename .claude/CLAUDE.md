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

- When working on PHP files see @.claude/docs/php.md
- When working on TypeScript files see @.claude/docs/ts.md

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
- `AM_DIR_CACHE` (`cache/` under `AM_BASE_DIR`, inside the web root) is only for cache content that is meant to be publicly servable over HTTP — resized images, page cache, etc. Anything that must **not** be exposed to the public (session state, rate-limiter state, debug logs, or any other non-public runtime/cache data) must instead go through `Automad\System\FileSystem::getTmpDir()` (backed by `AM_DIR_TMP`, resolved outside the web root, e.g. the system temp dir), never `AM_DIR_CACHE`. Existing examples: `AM_DEBUG_LOG_PATH` and `AM_LOGIN_RATE_LIMITER_PATH` (`Core/Config.php`) both use `AM_DIR_TMP`.
- MCP tools/resources are plain classes under `automad/src/server/System/Ai/Mcp/Tools/` and `automad/src/server/System/Ai/Mcp/Resources/`, each extending `Automad\System\Ai\Mcp\Tools\AbstractTool` / `Automad\System\Ai\Mcp\Resources\AbstractResource` (abstract classes, not interfaces — each lives in its own subdirectory/namespace next to the concrete classes it's discovered alongside). They are auto-discovered by `Automad\System\Ai\Mcp\Provider` — a dependency-free, glob-based mechanism modeled on `Automad\Engine\FeatureProvider` (glob the directory, `require_once` every file, filter `get_declared_classes()` via `is_subclass_of()` against the abstract base class) — deliberately **not** the `mcp/sdk` package's own attribute-based discovery, since that requires adding `symfony/finder` as a new Composer dependency. Add a new tool/resource by dropping in a new class there, not by editing `Automad\System\Ai\Mcp\Server`. Both base classes require `getTitle()`, `getDescription()`, and `getHandler(): callable` (the tool/resource's main handler; the SDK reflects on its parameters to derive the input schema and map call arguments by name) from subclasses, and provide sensible defaults for the rest: `getName()` (the machine name MCP clients call, `Str::sanitize($this->getTitle())`), `getAnnotations()` (`null` by default; override to return `Mcp\Schema\ToolAnnotations`/`Annotations`), and, on `AbstractResource` only, `getMimeType()` (defaults to `'application/json'`). `Automad\System\Ai\Mcp\Server`'s constructor builds each resource's URI itself as `'automad://' . $Resource->getName()` — resources don't declare their own URI.
- The MCP integration authenticates with a plain static Bearer token rather than OAuth — no authorize/token endpoints, no discovery metadata. Token-based auth itself lives in `Automad\Auth\Token\AccessToken` (issuance/verification) and `Automad\Auth\Token\AccessTokenConfig` (persistence), deliberately named generically rather than after MCP, since the same primitive is meant to be reusable for other future auth use cases — MCP is just its first consumer. Tokens are issued through the dashboard (`Controllers/API/AccessTokenController::addToken()`, which delegates to `AccessToken::issue()`; shown once, only a hash is persisted) and sent by the MCP client as a static `Authorization: Bearer <token>` header, the same pattern GitHub's MCP server and most personal-access-token-based integrations use. `McpController::render()` verifies incoming requests via `AccessToken::verifyRequest()` rather than doing bearer-token parsing/hashing itself. This was chosen for simplicity (a well-known, minimal auth pattern) — don't reintroduce OAuth for this endpoint without a real reason to.
- The MCP HTTP endpoint bridges the classic one-request-per-process PHP cycle to `mcp/sdk`'s transport-driven protocol handler using the SDK's own `Mcp\Server\Transport\StreamableHttpTransport` (PSR-7-based), not a hand-rolled transport. `nyholm/psr7` (`lib/composer.json`) supplies the PSR-7/PSR-17 implementation `StreamableHttpTransport` needs (auto-discovered via `php-http/discovery`). `Automad\Controllers\McpController::render()` builds a PSR-7 `ServerRequestInterface` from `php://input`/`getallheaders()`/`$_SERVER` with `Nyholm\Psr7\Factory\Psr17Factory`, and `Automad\System\Ai\Mcp\Server::handle()` passes it straight through to `StreamableHttpTransport`, emitting the resulting PSR-7 `ResponseInterface` back via `http_response_code()`/`header()`.
- `mcp/sdk` splits MCP protocol revisions into two "eras": handshake (`2024-11-05` through `2025-11-25` — the `initialize`-round-trip, `Mcp-Session-Id`-based protocol every real-world MCP client speaks today) and modern (currently only `2026-07-28`, per SEP-2575 — a stateless, per-request protocol with no `initialize`, versioned per-request via `_meta`, that no real client speaks yet). `Automad\System\Ai\Mcp\Server` deliberately serves both from the same endpoint, but pins the modern leg to `[ProtocolVersion::V2026_07_28]` via `Builder::setModernVersions()` instead of leaving it on the SDK's open-ended default (`null`, which silently grows to include any new modern revision a future `mcp/sdk` bump adds, before this codebase has tested against it). This is a pin, not a feature build: `mcp/sdk`'s `Builder::build()` already dispatches both eras through the same shared tool/resource/resourceTemplate handlers with zero Automad-side branching, so there's nothing else to build for modern-era support. `FileSessionStore` remains a handshake-era-only concern — the modern leg is stateless by design and never touches it — and must not be mistaken for a shared requirement when adding new tools/resources.
