# TypeScript Code Conventions

## Header

Every new TS file (`automad/src/client/**`) must start with the project's standard file header — the ASCII-art Automad logo followed by a copyright/license comment block. Copy the exact header from an existing file rather than retyping it, e.g. `automad/src/client/admin/index.ts`. It is the first thing in the file. Keep the copyright year range's end year current.

## Functions

Always prefer `export const myFunction = () => {}` over `export function myFunction() {}` for module functions that are not class methods.
