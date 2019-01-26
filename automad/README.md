# Automad Development Guide

## Installing Development Dependencies for the Dashboard

To compile all GUI `dist` files, **Gulp** and other dependencies must be installed first in `automad/gui`. Run `gulp` to compile all `dist` files.

    cd gui
    npm install
    gulp

## PHP Naming Convention

### Class Names and Class Files

All namespaces and class names are `PascalCased`. All corresponding filenames are lowercase. There is no word separator in class names and filenames of classes which are autoloaded by the Automad autoloader. 

    Automad\Core\FileSystem > automad/core/filesystem.php
    
However, unit test classes use an underscore to separate the `_Test` suffix from the actual tested class name for better readability. The corresponding files are lowercase as well and keep the underscore.

    Automad\Core\Parse_Test > automad/tests/core/parse_test.php
    
### Dashboard Context and AJAX Handler Files

All context and AJAX handlers filenames are lowercase and use an underscore as word separator.

    https://domain.com/dashboard?context=edit_shared > automad/gui/inc/context/edit_shared.php
    
### Variables

All class properties and normal variables are `camelCased`. Object variables of class instances are always named like the corresponding class and therefore are `PascalCased`.

    $this->pageCacheFile
    $Automad = new Automad()

### Methods

All methods are `camelCased`.

    $Cache->pageCacheIsApproved()
    
### Console Commands

All console commands and corresponding filenames are lowercase and don't have any word separator.

    php automad/console createuser > automad/cli/createuser.php
    
### Composer Packages

Composer packages are excluded from Automad's internal naming convention in terms of directory structure, filenames and classes.  