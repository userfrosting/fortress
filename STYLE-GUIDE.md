# Style guide for contributing

## PHP

All PHP contributions must adhere to [PSR-1](http://www.php-fig.org/psr/psr-1/) and [PSR-2](http://www.php-fig.org/psr/psr-2/) specifications.

In addition:

### Documentation

- All documentation blocks must adhere to the [PHPDoc](https://phpdoc.org/) format and syntax.
- All PHP files MUST contain the following documentation block immediately after the opening `<?php` tag:

```
/**
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */
 ```

### Classes

- All classes MUST be prefaced with a documentation block containing a description and the author(s) of that class.  You SHOULD add other descriptive properties as well.
- All class members and methods MUST be prefaced with a documentation block.  Any parameters and return values MUST be documented.
- The contents of a class should be organized in the following order: constants, member variables, constructor, other magic methods, public methods, protected methods, private methods.
- Within each of the categories above, variables/methods should be alphabetized.  See http://stackoverflow.com/a/3366429/2970321.
- Setter methods SHOULD return the parent object.

### Variables

 - All class member variables and local variables MUST be declared in `camelCase`.

### Arrays

 - Array keys MUST be defined using `snake_case`.  This is so they can be referenced in Twig and other templating languages.
 - Array keys MUST NOT contain `.`.  This is because `.` is a reserved operator in Laravel and Twig's [dot syntax](https://medium.com/@assertchris/dot-notation-3fd3e42edc61).
 - Multidimensional arrays SHOULD be referenced using dot syntax whenever possible.  So, instead of doing `$myArray['person1']['email']`, you should use `$myArray['person1.email']` if your array structure supports it.

## Automatically fixing coding style with PHP-CS-Fixer

[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) can be used to automatically fix PHP code styling. UserFrosting provides a project specific configuration file ([`.php_cs`](.php_cs)) with a set of rules reflecting our style guidelines. This tool should be used before submitting any code change to assure the style guidelines are met. Every sprinkles will also be parsed by the fixer.

PHP-CS-Fixer is automatically loaded by Composer and can be used from the UserFrosting root directory :

```
vendor/bin/php-cs-fixer fix
```
