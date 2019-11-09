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
use UserFrosting\Fortress\Adapter\FormValidationAdapter;
use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\I18n\Translator;
use UserFrosting\I18n\DictionaryInterface;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\Support\Repository\Repository;

class FormValidationAdapterTest extends TestCase
{
    protected $translator;

    public function setUp()
    {
        // Create a message translator
        $this->translator = new Translator(new DictionaryStubA());
    }

    public function testValidateEmail()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'email' => [
                'validators' => [
                    'email' => [
                        'message' => 'Not a valid email address...we think.',
                    ],
                ],
            ],
        ]);

        $expectedResult = [
            'email' => [
                'validators' => [
                    'emailAddress' => [
                        'message' => 'Not a valid email address...we think.',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with stringEncode as true
        $result = $adapter->rules('json', false);
        $this->assertEquals($expectedResult, $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['email' => 'data-fv-emailaddress=true data-fv-emailaddress-message="Not a valid email address...we think." '];
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * N.B.: equals is not a supported validator in FormValidationAdapter.
     * Let's test what's happening when this happens.
     */
    public function testValidateEquals()
    {
        // Arrange
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

        $expectedResult = [
            'voles' => [
                'validators' => [
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['voles' => ''];
        $this->assertEquals($expectedResult, $result);
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

        $expectedResult = [
            'species' => [
                'validators' => [
                    'notEmpty' => [
                        'message' => 'Please tell us your species.',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['species' => 'data-fv-notempty=true data-fv-notempty-message="Please tell us your species." '];
        $this->assertEquals($expectedResult, $result);
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

        $expectedResult = [
            'screech' => [
                'validators' => [
                    'stringLength' => [
                        'message' => 'Your screech must be between 5 and 10 characters long.',
                        'min'     => 5,
                        'max'     => 10,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['screech' => 'data-fv-stringlength=true data-fv-stringlength-message="Your screech must be between {{min}} and {{max}} characters long." data-fv-stringlength-min=5 data-fv-stringlength-max=10 '];
        $this->assertEquals($expectedResult, $result);
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

        $expectedResult = [
            'screech' => [
                'validators' => [
                    'stringLength' => [
                        'message' => 'Your screech must be at least 5 characters long.',
                        'min'     => 5,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['screech' => 'data-fv-stringlength=true data-fv-stringlength-message="Your screech must be at least {{min}} characters long." data-fv-stringlength-min=5 '];
        $this->assertEquals($expectedResult, $result);
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

        $expectedResult = [
            'screech' => [
                'validators' => [
                    'stringLength' => [
                        'message' => 'Your screech must be no more than 10 characters long.',
                        'max'     => 10,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['screech' => 'data-fv-stringlength=true data-fv-stringlength-message="Your screech must be no more than {{max}} characters long." data-fv-stringlength-max=10 '];
        $this->assertEquals($expectedResult, $result);
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

        $expectedResult = [
            'voles' => [
                'validators' => [
                    'integer' => [
                        'message' => 'Voles must be numeric.',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['voles' => 'data-fv-integer=true data-fv-integer-message="Voles must be numeric." '];
        $this->assertEquals($expectedResult, $result);
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

        $expectedResult = [
            'accuracy' => [
                'validators' => [
                    'numeric' => [
                        'message' => 'Sorry, your strike accuracy must be a number.',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);
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

        $expectedResult = [
            'voles' => [
                'validators' => [
                    'between' => [
                        'message' => 'You must catch 5 - 10 voles.',
                        'min'     => 5,
                        'max'     => 10,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['voles' => 'data-fv-between=true data-fv-between-message="You must catch {{min}} - {{max}} voles." data-fv-between-min=5 data-fv-between-max=10 '];
        $this->assertEquals($expectedResult, $result);
    }

    public function testValidateRangeMin()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'range' => [
                        'min'     => 5,
                        'message' => 'You must catch at least {{min}} voles.',
                    ],
                ],
            ],
        ]);

        $expectedResult = [
            'voles' => [
                'validators' => [
                    'greaterThan' => [
                        'message' => 'You must catch at least 5 voles.',
                        'min'     => 5,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['voles' => 'data-fv-greaterthan=true data-fv-greaterthan-message="You must catch at least {{min}} voles." data-fv-greaterthan-value=5 '];
        $this->assertEquals($expectedResult, $result);
    }

    public function testValidateRangeMax()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'range' => [
                        'max'     => 10,
                        'message' => 'You must catch no more than {{max}} voles.',
                    ],
                ],
            ],
        ]);

        $expectedResult = [
            'voles' => [
                'validators' => [
                    'lessThan' => [
                        'message' => 'You must catch no more than 10 voles.',
                        'max'     => 10,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['voles' => 'data-fv-lessthan=true data-fv-lessthan-message="You must catch no more than {{max}} voles." data-fv-lessthan-value=10 '];
        $this->assertEquals($expectedResult, $result);
    }

    public function testValidateArray()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'voles' => [
                'validators' => [
                    'array' => [
                        'min'     => 5,
                        'max'     => 10,
                        'message' => 'You must choose between {{min}} and {{max}} voles.',
                    ],
                ],
            ],
        ]);

        $expectedResult = [
            'voles' => [
                'validators' => [
                    'choice' => [
                        'message' => 'You must choose between 5 and 10 voles.',
                        'min'     => 5,
                        'max'     => 10,
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['voles' => 'data-fv-choice=true data-fv-choice-message="You must choose between {{min}} and {{max}} voles." data-fv-choice-min=5 data-fv-choice-max=10 '];
        $this->assertEquals($expectedResult, $result);
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
            'passwordc' => [
                'validators' => [],
            ],
        ]);

        $expectedResult = [
            'password' => [
                'validators' => [
                    'identical' => [
                        'message' => "The value of this field does not match the value of the 'passwordc' field.",
                        'field'   => 'passwordc',
                    ],
                ],
            ],
            'passwordc' => [
                'validators' => [],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = [
            'password'  => 'data-fv-identical=true data-fv-identical-message="The value of this field does not match the value of the \'{{field}}\' field." ',
            'passwordc' => 'data-fv-identical=true data-fv-identical-message="The value of this field does not match the value of the \'{{field}}\' field." data-fv-identical-field=password ',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    public function testValidateMatchesNoFields()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'password' => [
                'validators' => [
                    'matches' => [
                        'message' => "The value of this field does not match the value of the '{{field}}' field.",
                    ],
                ],
            ],
        ]);

        $expectedResult = [
            'password' => [
                'validators' => [
                    'identical' => [
                        'message' => "The value of this field does not match the value of the '' field.",
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $this->assertEquals(null, $result);
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

        $expectedResult = [
            'password' => [
                'validators' => [
                    'different' => [
                        'message' => 'Your password cannot be the same as your username.',
                        'field'   => 'user_name',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);
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

        $expectedResult = [
            'genus' => [
                'validators' => [
                    'regexp' => [
                        'message' => 'Sorry, that is not one of the permitted genuses.',
                        'regexp'  => '^Megascops|Bubo|Glaucidium|Tyto|Athene$',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);
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

        $expectedResult = [
            'genus' => [
                'validators' => [
                    'regexp' => [
                        'message' => 'Sorry, it would appear that you are not an owl.',
                        'regexp'  => '^(?!Myodes|Microtus|Neodon|Alticola$).*$',
                    ],
                ],
            ],
        ];

        // Act
        $adapter = new FormValidationAdapter($schema, $this->translator);
        $result = $adapter->rules();

        // Assert
        $this->assertEquals(json_encode($expectedResult, JSON_PRETTY_PRINT), $result);
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
        $adapter = new FormValidationAdapter($schema, $this->translator);

        // Test with html5 format
        $result = $adapter->rules('html5');
        $expectedResult = ['plumage' => ''];
        $this->assertEquals($expectedResult, $result);
    }
}

class DictionaryStubA extends Repository implements DictionaryInterface
{
    public function __construct()
    {
    }

    public function getDictionary(): array
    {
        return [];
    }

    public function getLocale(): LocaleInterface
    {
    }
}
