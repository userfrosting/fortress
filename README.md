# Fortress 4

[![Latest Version](https://img.shields.io/github/release/userfrosting/fortress.svg)](https://github.com/userfrosting/fortress/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Join the chat at https://chat.userfrosting.com/channel/support](https://chat.userfrosting.com/api/v1/shield.svg?name=UserFrosting)](https://chat.userfrosting.com/channel/support)
[![Donate](https://img.shields.io/badge/Open%20Collective-Donate-blue.svg)](https://opencollective.com/userfrosting#backer)

| Branch | Build | Coverage | Style |
| ------ |:-----:|:--------:|:-----:|
| [master][Fortress]  | [![][fortress-master-build]][fortress-travis] | [![][fortress-master-codecov]][fortress-codecov] | [![][fortress-style-master]][fortress-style] |
| [develop][fortress-develop] | [![][fortress-develop-build]][fortress-travis] | [![][fortress-develop-codecov]][fortress-codecov] | [![][fortress-style-develop]][fortress-style] |

<!-- Links -->
[Fortress]: https://github.com/userfrosting/fortress
[fortress-develop]: https://github.com/userfrosting/fortress/tree/develop
[fortress-version]: https://img.shields.io/github/release/userfrosting/fortress.svg
[fortress-master-build]: https://github.com/userfrosting/fortress/workflows/Build/badge.svg?branch=master
[fortress-master-codecov]: https://codecov.io/gh/userfrosting/fortress/branch/master/graph/badge.svg
[fortress-develop-build]: https://github.com/userfrosting/fortress/workflows/Build/badge.svg?branch=develop
[fortress-develop-codecov]: https://codecov.io/gh/userfrosting/fortress/branch/develop/graph/badge.svg
[fortress-releases]: https://github.com/userfrosting/fortress/releases
[fortress-travis]: https://github.com/userfrosting/fortress/actions?query=workflow%3ABuild
[fortress-codecov]: https://codecov.io/gh/userfrosting/fortress
[fortress-style-master]: https://github.styleci.io/repos/30551954/shield?branch=master&style=flat
[fortress-style-develop]: https://github.styleci.io/repos/30551954/shield?branch=develop&style=flat
[fortress-style]: https://github.styleci.io/repos/30551954

If you simply want to show that you like this project, or want to remember it for later, you should **star**, not **fork**, this repository.  Forking is only for when you are ready to create your own copy of the code to work on.

### By [Alex Weissman](https://alexanderweissman.com)

Copyright (c) 2015-2019

A schema-driven system for elegant whitelisting, transformation and validation of user input on both the client and server sides from a unified set of rules.

## Introduction

Data from the outside world is the Achilles' heel of modern interactive web services and applications. Code injection, cross-site scripting (XSS), CSRF, and many other types of malicious attacks are successful when a web application accepts user input that it shouldn't have, or fails to neutralize the damaging parts of the input. Even non-malicious users can inadvertently submit something that breaks your web service, causing it to behave in some unexpected way.

For the sake of both security and quality user experience, it is important for a web developer to do two things:

1. Decide exactly what type of input your application should accept, and;
2. Decide how your application should behave when it receives something that violates those rules.

Sounds simple, right? Unfortunately, even experienced developers often slip up, allowing a malicious user to execute SQL or PHP on the application's server (or, in the case of XSS and CSRF attacks, allow a user to trick _other users_ into executing malicious code).

Part of the problem is that this kind of filtering must be done at every point in the application where the user can submit raw data to the server. A modern web application might accept hundreds of different types of POST requests, and it can become extremely tedious to code the rules for each request manually. Much of this work must also be done on both the client side (for user experience) and the server side (for security).

Fortress solves this problem by providing a uniform interface for validating raw user input on both the client side (in Javascript) and on the server side (in PHP) using a single unified set of rules. All you have to do is create a **request schema**, which defines what fields you're expecting the user to submit, and [rules](https://github.com/userfrosting/wdvss) for how to handle the contents of those fields. For example, you might want to check that an email address is well-formed. The request schema, which is simply a YAML or JSON document, makes it easy to manipulate these rules in one place.

The request schema can be applied on the server side to received request data, but also be transformed to formats compatible with client-side validation libraries such as [the jQuery Validation plugin](https://jqueryvalidation.org/), making it easy to perform client- and server-side validation without having to write every rule twice.

An example request schema, written using the [WDVSS standard](https://github.com/userfrosting/wdvss):

**schema.yaml**

```
name:
    validators:
        length:
            min: 1
            max: 200
            message: Please enter a name between 1 and 200 characters.
        required :
            message : Please specify your name.

email:
    validators:
        required:
            message: Please specify your email address.

        length:
            min: 1
            max: 150
            message: Please enter an email address between 1 and 150 characters.

        email:
            message : That does not appear to be a valid email address.

message:
    validators:
        required:
            message: Please enter a message
```

## Dependencies

- PHP 5.6+
- [Valitron (server-side validation)](https://github.com/vlucas/valitron)
- [HTML Purifier](https://github.com/ezyang/htmlpurifier)
- [Symfony YAML Parser](http://symfony.com/doc/current/components/yaml.html)
- userfrosting/i18n
- userfrosting/support

## Installation

### To install with composer:

1. If you haven't already, get [Composer](http://getcomposer.org/) and [install it](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx) - preferably, globally.
2. Require Fortress, either by running `php composer.phar require alexweissman/fortress`, or by creating a `composer.json` file:

```
{
    "require": {
        "php": ">=5.6.0",
        "userfrosting/fortress": "^4.2.0"
    }
}
```

and running `composer install`.

3. Include the `vendor/autoload.php` file in your project:

```
require dirname(__DIR__) . '/vendor/autoload.php';
```

## Usage

### Request schema

To read a YAML or JSON schema, use a `YamlFileLoader`:

```
$loader = new \UserFrosting\Support\Repository\Loader\YamlFileLoader('schema/forms/contact.yaml');
```

To use it, it must be read and loaded into a `RequestSchemaRepository` object:

```
$schema = new \UserFrosting\Fortress\RequestSchema\RequestSchemaRepository($loader->load());
```

You can add additional validation rules to a schema at runtime, if you wish:

```
$schema->addValidator("puppies", "required");

$schema->addValidator("minions", "range", [
    "min" => 0,
    "max" => 20,
    "message" => "Not enough minions"
]);

$schema->addValidator("email", "length", [
    "min" => 1,
    "max" => 100,
    "message" => "ACCOUNT_EMAIL_CHAR_LIMIT"
]);
```

### Data transformation

The data transformer performs the following tasks:

1. Whitelisting of input array against the schema. By default, any parameters not listed in the schema will be filtered out. Other options are "error" and "skip".
2. Perform a series of transformations on the input data. For example, `trim` or `purify`.
3. Set any default values for fields in the schema which are not present in the input array.

```
$post = [
    "puppies" => "<script>I'm definitely really a puppy  </script>0  ",
    "horses" => "seven pretty horses"
];

$transformer = new \UserFrosting\Fortress\RequestDataTransformer($schema);

// Transform, and print transformed data for demo purposes
$transformedData = $transformer->transform($post, "skip");

echo "<h2>Transformed data</h2>";
echo "<pre>";
print_r($transformedData);
echo "</pre>";
```

### Server-side data validation

To process an array of user input, create a `ServerSideValidator` object with the schema and a translator object.

### Translator object

Fortress requires a `Translator` (see [i18n](https://github.com/userfrosting/i18n)) object to translate message keys that may appear in rules:

```
$locale = new \UserFrosting\I18n\Locale('en_US');
$dictionary = new \UserFrosting\I18n\Dictionary($locale, $this->ci->locator);
$translator = new \UserFrosting\I18n\Translator($dictionary);
```

Then, call `validate` on the input array. `validate` will return false if any of the rules are failed. Call `errors` to get the list of generated error messages. You might want to store these error messages to a flash messaging system so they can be shown to the user.

```
$validator = new \UserFrosting\Fortress\ServerSideValidator($schema, $translator);

if (!$validator->validate($transformedData)) {
    echo "<h2>Validation results</h2>";
    echo "<pre>";
    print_r($validator->errors());
    echo "</pre>";
}
```

### Client-side data validation

When generating a page or form, you will use one of the `Adapter` classes to generate a compatible set of rules from your WDVSS schema:

```
// Test client validators
$clientVal = new \UserFrosting\Fortress\Adapter\JqueryValidationAdapter($schema, $translator);
echo "<h2>Client-side validation schema (JSON)</h2>";
echo "<pre>";
print_r($clientVal->rules());
echo "</pre>";
```

### Add Namespace to the validation field names

You can also add an array prefix to the field names to generate validation rules for the input schema that will wrap all the field names with the namespace of the form for which the validation rules are being generated.

```
// Test client validators
$clientVal = new \UserFrosting\Fortress\Adapter\JqueryValidationAdapter($schema, $translator);
echo "<h2>Client-side validation schema (JSON)</h2>";
echo "<pre>";
print_r($clientVal->rules('json',false,'mycoolform1'));
echo "</pre>";
```

This will generate validation rules with field names `mycoolform1[<fieldname>] : { .... }` instead of `<fieldname> : { .... }`

This comes in handy when you are generating validation rules for multiple forms or form sections on the same page

### Message keys

The `message` for a rule can be either a plain string, or a [translatable message key](https://github.com/userfrosting/i18n).

In the definitions of translatable message keys, the keyword "self" is reserved to refer to the name of the field being validated. Thus, a message like this:

"MIN_LENGTH" => "The field '{{self}}' must be at least {{min}} characters long"

for a field defined as:

```
tagline:
  validators:
    length:
      min: 10
      message: MIN_LENGTH
```

Would translate to:

"The field 'tagline' must be at least 10 characters long"

### Limit rules to server or client only

Sometimes, you only want a validation rule to be applied server-side but not in Javascript on the client side, or vice versa. For example, there may be forms that contain hidden data that needs to be validated on the server-side, but is not directly manipulated by the user in the browser. Thus, these fields would not need client-side validation rules.

Alternatively, there might be fields that appear in the form that should be validated for the sake of user experience, but are not actually used by (or even sent to) the server.

To accomplish this, each validation rule can now accept a `domain` property. Setting to "server" will have it only applied server-side. Setting to "client" will have it only appear in the client-side rules. If not specified, rules will be applied both server- and client-side by default. You can also set this explicitly with the value "both".

## [Style Guide](STYLE-GUIDE.md)

## [Testing](RUNNING_TESTS.md)
