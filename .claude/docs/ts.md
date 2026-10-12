# Frontend TypeScript Code Conventions

## Header

Every new TS file (`automad/src/client/**`) must start with the project's standard file header — the ASCII-art Automad logo followed by a copyright/license comment block. Copy the exact header from an existing file rather than retyping it, e.g. `automad/src/client/admin/index.ts`. It is the first thing in the file. Keep the copyright year range's end year current.

## Functions

Always prefer `export const myFunction = () => {}` over `export function myFunction() {}` for module functions that are not class methods.

## Strings and Enums

The frontend never hardcodes CSS class names, `am-*` custom attribute/element names, or server-controller identifiers as literal strings — three parallel `const enum`s centralize them, all re-exported through the `@/admin/core` barrel (`automad/src/client/admin/core/index.ts`):

- **`CSS`** (`automad/src/client/admin/core/css.ts`) — maps camelCase keys to BEM-ish class names (`am-c-` component, `am-e-` element, `am-f-` form, `am-u-` utility, `am-l-` layout), e.g. `CSS.cardTitle = 'am-c-card__title'`. Always reference `CSS.xyz`, never write `'am-c-...'` literals.
- **`Attr`** (`automad/src/client/admin/core/html.ts`) — maps camelCase keys to `am-*` attribute names used for declarative component wiring (`Attr.api`, `Attr.event`, `Attr.target`, `Attr.confirm`, `Attr.url`, etc.), e.g. `Attr.api = 'am-api'`. Custom elements read these off `this.getAttribute(Attr.x)`/template interpolation (`${Attr.api}="..."`) instead of raw attribute-name strings. This file also configures DOMPurify's custom-element/attribute allowlist (`^(am|sortable)-`), so any new `am-*` attribute should be added here, not invented ad hoc.
- **Controller enums** (`automad/src/client/common/controllers.ts`) — one `const enum` per PHP API controller (`PageController`, `PageCollectionController`, `ComponentController`, `SharedController`, `PageTrashController`, etc.), each case a string like `'PageController::publish'`. These are passed as `Attr.api` values on `<am-form>` or as the first argument to `requestApi()`, and are resolved server-side by `RequestHandler` via `call_user_func('Class::method')` against a `public static` method on the matching `Automad\Controllers\Api\*` class — so adding a new server controller method always means adding a matching case here with the exact same string, never calling the API with a literal `'ClassName::method'` string inline.
