<?php

/*
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Tests;

use PHPUnit\Framework\TestCase;
use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\I18n\MessageTranslator;

class ServerSideValidatorTest extends TestCase
{
    protected $translator;

    public function setUp()
    {
        // Create a message translator
        $this->translator = new MessageTranslator();
    }

    public function testValidateNoValidators()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'email' => [],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'email' => 'david@owlfancy.com',
        ]);

        // Check passing validation
        $this->assertTrue($result);
    }

    public function testValidateEmail()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'email' => [
                'validators' => [
                    'email' => [
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'email' => 'david@owlfancy.com',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('email', 'email'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validation
        $this->assertFalse($validator->validate([
            'email' => 'screeeech',
        ]));
    }

    public function testValidateArray()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'screech' => [
                'validators' => [
                    'array' => [
                        'message' => 'Array must be an array.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'screech' => ['foo', 'bar'],
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('array', 'screech'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validation
        $this->assertFalse($validator->validate([
            'screech' => 'screeeech',
        ]));
    }

    public function testValidateEquals()
    {
        // Arrange
        // TODO: Add missing messages for custom rules.  Maybe upgrade the version of Valitron first.
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'equals' => [
                        'value'         => 8,
                        'caseSensitive' => false,
                        'message'       => 'Voles must be equal to {{value}}.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'voles' => 8,
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('equalsValue', 'voles'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validation
        $this->assertFalse($validator->validate([
            'voles' => 3,
        ]));
    }

    public function testValidateInteger()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'integer' => [
                        'message' => 'Voles must be numeric.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'voles' => 8,
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('integer', 'voles'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validations
        $this->assertFalse($validator->validate([
            'voles' => 'yes',
        ]));

        $this->assertFalse($validator->validate([
            'voles' => 0.5,
        ]));
    }

    public function testValidateLengthBetween()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'screech' => [
                'validators' => [
                    'length' => [
                        'min'     => 5,
                        'max'     => 10,
                        'message' => 'Your screech must be between {{min}} and {{max}} characters long.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'screech' => 'cawwwwww',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('lengthBetween', 'screech'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validations
        $this->assertFalse($validator->validate([
            'screech' => 'caw',
        ]));

        $this->assertFalse($validator->validate([
            'screech' => 'cawwwwwwwwwwwwwwwwwww',
        ]));
    }

    public function testValidateLengthMin()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'screech' => [
                'validators' => [
                    'length' => [
                        'min'     => 5,
                        'message' => 'Your screech must be at least {{min}} characters long.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'screech' => 'cawwwwww',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('lengthMin', 'screech'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validations
        $this->assertFalse($validator->validate([
            'screech' => 'caw',
        ]));
    }

    public function testValidateLengthMax()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'screech' => [
                'validators' => [
                    'length' => [
                        'max'     => 10,
                        'message' => 'Your screech must be no more than {{max}} characters long.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'screech' => 'cawwwwww',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('lengthMax', 'screech'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'screech' => 'cawwwwwwwwwwwwwwwwwww',
        ]));
    }

    public function testValidateMatches()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'password' => [
                'validators' => [
                    'matches' => [
                        'field'   => 'passwordc',
                        'message' => "The value of this field does not match the value of the '{{field}}' field.",
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'password'  => 'secret',
            'passwordc' => 'secret',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('equals', 'password'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'password'  => 'secret',
            'passwordc' => 'hoothoot',
        ]));
    }

    public function testValidateMemberOf()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'genus' => [
                'validators' => [
                    'member_of' => [
                        'values'  => ['Megascops', 'Bubo', 'Glaucidium', 'Tyto', 'Athene'],
                        'message' => 'Sorry, that is not one of the permitted genuses.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'genus' => 'Megascops',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('in', 'genus'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'genus' => 'Dolomedes',
        ]));
    }

    public function testValidateNoLeadingWhitespace()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'user_name' => [
                'validators' => [
                    'no_leading_whitespace' => [
                        'message' => '{{self}} cannot begin with whitespace characters',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'user_name' => 'alexw',
        ]);

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'user_name' => ' alexw',
        ]));
    }

    public function testValidateNoTrailingWhitespace()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'user_name' => [
                'validators' => [
                    'no_trailing_whitespace' => [
                        'message' => '{{self}} cannot end with whitespace characters',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'user_name' => 'alexw',
        ]);

        // Check passing validation
        $this->assertTrue($result);

        // Should still allow starting with whitespace
        $this->assertTrue($validator->validate([
            'user_name' => ' alexw',
        ]));

        $this->assertFalse($validator->validate([
            'user_name' => 'alexw ',
        ]));
    }

    public function testValidateNotEquals()
    {
        // Arrange
        // TODO: Add missing messages for custom rules.  Maybe upgrade the version of Valitron first.
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'not_equals' => [
                        'value'         => 0,
                        'caseSensitive' => false,
                        'message'       => 'Voles must be not be equal to {{value}}.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'voles' => 8,
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('notEqualsValue', 'voles'));

        // Check passing validation
        $this->assertTrue($result);

        // Check failing validation
        $this->assertFalse($validator->validate([
            'voles' => 0,
        ]));
    }

    public function testValidateNotMatches()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'password' => [
                'validators' => [
                    'not_matches' => [
                        'field'   => 'user_name',
                        'message' => 'Your password cannot be the same as your username.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'password'  => 'secret',
            'user_name' => 'alexw',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('different', 'password'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'password'  => 'secret',
            'user_name' => 'secret',
        ]));
    }

    public function testValidateNotMemberOf()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'genus' => [
                'validators' => [
                    'not_member_of' => [
                        'values'  => ['Myodes', 'Microtus', 'Neodon', 'Alticola'],
                        'message' => 'Sorry, it would appear that you are not an owl.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'genus' => 'Megascops',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('notIn', 'genus'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'genus' => 'Myodes',
        ]));
    }

    public function testValidateNumeric()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'accuracy' => [
                'validators' => [
                    'numeric' => [
                        'message' => 'Sorry, your strike accuracy must be a number.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'accuracy' => 0.99,
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('numeric', 'accuracy'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertTrue($validator->validate([
            'accuracy' => '0.99',
        ]));

        $this->assertTrue($validator->validate([
            'accuracy' => '',
        ]));

        $this->assertFalse($validator->validate([
            'accuracy' => false,
        ]));

        $this->assertFalse($validator->validate([
            'accuracy' => 'yes',
        ]));
    }

    public function testValidateRange()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'range' => [
                        'min'     => 5,
                        'max'     => 10,
                        'message' => 'You must catch {{min}} - {{max}} voles.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'voles' => 6,
        ]);

        // Check that the correct Valitron rules were generated
        $this->assertTrue($validator->hasRule('min', 'voles'));
        $this->assertTrue($validator->hasRule('max', 'voles'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'voles' => 2,
        ]));

        $this->assertFalse($validator->validate([
            'voles' => 10000,
        ]));

        $this->assertFalse($validator->validate([
            'voles' => 'yes',
        ]));
    }

    public function testValidateRegex()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'screech' => [
                'validators' => [
                    'regex' => [
                        'regex'   => '^who(o*)$',
                        'message' => 'You did not provide a valid screech.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'screech' => 'whooooooooo',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('regex', 'screech'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'screech' => 'whoot',
        ]));

        $this->assertFalse($validator->validate([
            'screech' => 'ribbit',
        ]));
    }

    public function testValidateRequired()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'species' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please tell us your species.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'species' => 'Athene noctua',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('required', 'species'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertFalse($validator->validate([
            'species' => '',
        ]));

        $this->assertFalse($validator->validate([]));
    }

    public function testValidateTelephone()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'phone' => [
                'validators' => [
                    'telephone' => [
                        'message' => 'Whoa there, check your phone number again.',
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'phone' => '1(212)-999-2345',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('phoneUS', 'phone'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertTrue($validator->validate([
            'phone' => '212 999 2344',
        ]));

        $this->assertTrue($validator->validate([
            'phone' => '212-999-0983',
        ]));

        $this->assertFalse($validator->validate([
            'phone' => '111-123-5434',
        ]));

        $this->assertFalse($validator->validate([
            'phone' => '212 123 4567',
        ]));

        $this->assertFalse($validator->validate([
            'phone' => '',
        ]));
    }

    public function testValidateUri()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'website' => [
                'validators' => [
                    'uri' => [
                        'message' => "That's not even a valid URL...",
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'website' => 'http://www.owlfancy.com',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('url', 'website'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertTrue($validator->validate([
            'website' => 'http://owlfancy.com',
        ]));

        $this->assertTrue($validator->validate([
            'website' => 'https://learn.userfrosting.com',
        ]));

        // Note that we require URLs to begin with http(s)://
        $this->assertFalse($validator->validate([
            'website' => 'www.owlfancy.com',
        ]));

        $this->assertFalse($validator->validate([
            'website' => 'owlfancy.com',
        ]));

        $this->assertFalse($validator->validate([
            'website' => 'owls',
        ]));

        $this->assertFalse($validator->validate([
            'website' => '',
        ]));
    }

    public function testValidateUsername()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'user_name' => [
                'validators' => [
                    'username' => [
                        'message' => "Sorry buddy, that's not a valid username.",
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'user_name' => 'alex.weissman',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('username', 'user_name'));

        // Check passing validation
        $this->assertTrue($result);

        $this->assertTrue($validator->validate([
            'user_name' => 'alexweissman',
        ]));

        $this->assertTrue($validator->validate([
            'user_name' => 'alex-weissman-the-wise',
        ]));

        // Note that we require URLs to begin with http(s)://
        $this->assertFalse($validator->validate([
            'user_name' => "<script>alert('I got you');</script>",
        ]));

        $this->assertFalse($validator->validate([
            'user_name' => '#owlfacts',
        ]));

        $this->assertFalse($validator->validate([
            'user_name' => 'Did you ever hear the tragedy of Darth Plagueis the Wise?',
        ]));

        $this->assertFalse($validator->validate([
            'user_name' => '',
        ]));
    }

    public function testDomainRulesClientOnly()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'plumage' => [
                'validators' => [
                    'required' => [
                        'domain'  => 'client',
                        'message' => "Are you sure you don't want to show us your plumage?",
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([]);

        // Check that the correct Valitron rule was generated
        $this->assertFalse($validator->hasRule('required', 'plumage'));

        // Check passing validation
        $this->assertTrue($result);
    }

    public function testDomainRulesServerOnly()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'plumage' => [
                'validators' => [
                    'required' => [
                        'domain'  => 'server',
                        'message' => "Are you sure you don't want to show us your plumage?",
                    ],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('required', 'plumage'));

        // Check passing validation
        $this->assertFalse($result);
    }

    /**
     * @depends testValidateUsername
     */
    public function testValidateWithNoValidatorMessage()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'user_name' => [
                'validators' => [
                    'username' => [],
                ],
            ],
        ]);

        // Act
        $validator = new ServerSideValidator($schema, $this->translator);

        $result = $validator->validate([
            'user_name' => 'alex.weissman',
        ]);

        // Check that the correct Valitron rule was generated
        $this->assertTrue($validator->hasRule('username', 'user_name'));

        // Check passing validation
        $this->assertTrue($result);
    }
}
