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
use UserFrosting\Support\Repository\Loader\YamlFileLoader;

class RequestSchemaRepositoryTest extends TestCase
{
    protected $basePath;

    protected $contactSchema;

    public function setUp()
    {
        $this->basePath = __DIR__.'/data';

        $this->contactSchema = [
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                ],
            ],
        ];
    }

    public function testReadJsonSchema()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.json');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $result = $schema->all();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }

    public function testReadYamlSchema()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $result = $schema->all();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }

    public function testSetDefault()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->setDefault('message', 'I require more voles.');
        $result = $schema->all();

        // Assert
        $contactSchema = [
            'message' => [
                'default'    => 'I require more voles.',
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                ],
            ],
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testSetDefaultWithMissingField()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->setDefault('foo', 'bar');
        $result = $schema->all();

        // Assert
        $contactSchema = [
              'foo' => [
                  'default' => 'bar',
              ],
          ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testAddValidator()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->addValidator('message', 'length', [
            'max'     => 10000,
            'message' => 'Your message is too long!',
        ]);
        $result = $schema->all();

        // Assert
        $contactSchema = [
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                    'length' => [
                        'max'     => 10000,
                        'message' => 'Your message is too long!',
                    ],
                ],
            ],
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testAddValidatorWithMissingField()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->addValidator('foo', 'length', [
            'max'     => 10000,
            'message' => 'Your message is too long!',
        ]);
        $result = $schema->all();

        // Assert
        $contactSchema = [
            'foo' => [
                'validators' => [
                    'length' => [
                        'max'     => 10000,
                        'message' => 'Your message is too long!',
                    ],
                ],
            ],
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testRemoveValidator()
    {
        // Arrange
        $schema = new RequestSchemaRepository([
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                    'length' => [
                        'max'     => 10000,
                        'message' => 'Your message is too long!',
                    ],
                ],
            ],
        ]);

        // Act
        $schema->removeValidator('message', 'required');
        // Check that attempting to remove a rule that doesn't exist, will have no effect
        $schema->removeValidator('wings', 'required');
        $schema->removeValidator('message', 'telephone');

        $result = $schema->all();

        // Assert
        $contactSchema = [
            'message' => [
                'validators' => [
                    'length' => [
                        'max'     => 10000,
                        'message' => 'Your message is too long!',
                    ],
                ],
            ],
        ];

        $this->assertEquals($contactSchema, $result);
    }

    public function testSetTransformation()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->setTransformations('message', ['purge', 'owlify']);
        $result = $schema->all();

        // Assert
        $contactSchema = [
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                ],
                'transformations' => [
                    'purge',
                    'owlify',
                ],
            ],
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testSetTransformationNotAnArray()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->setTransformations('message', 'purge');
        $result = $schema->all();

        // Assert
        $contactSchema = [
            'message' => [
                'validators' => [
                    'required' => [
                        'message' => 'Please enter a message',
                    ],
                ],
                'transformations' => [
                    'purge',
                ],
            ],
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testSetTransformationWithMissingField()
    {
        // Arrange
        $loader = new YamlFileLoader($this->basePath.'/contact.yaml');
        $schema = new RequestSchemaRepository($loader->load());

        // Act
        $schema->setTransformations('foo', ['purge', 'owlify']);
        $result = $schema->all();

        // Assert
        $contactSchema = [
            'foo' => [
                'transformations' => [
                    'purge',
                    'owlify',
                ],
            ],
        ];
        $this->assertArraySubset($contactSchema, $result);
    }
}
