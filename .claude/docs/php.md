# PHP Code Conventions

## Header

Every new PHP file (`automad/src/server/**`) must start with the project's standard file header — the ASCII-art Automad logo followed by a copyright/license comment block. Copy the exact header from an existing file rather than retyping it, e.g. `automad/src/server/App.php`. The header comment comes after the opening `<?php` tag. Keep the copyright year range's end year current.

## Contructors

Never use constructor parameter promotion for classes. Instead use the classic way to define class properties inside the constructor.

## Sorting

Sort methods and properties according to the @.php-cs-fixer.php config file in the following order:

1. constants alphabetically
2. public properties alphabetically
3. private properties alphabetically
4. public methods alphabetically
5. private methods alphabetically
