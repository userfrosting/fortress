<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Fortress\RequestSchema;

class RequestSchemaTest extends TestCase
{
    protected $basePath;

    protected $contactSchema;
    
    public function setUp()
    {
        $this->basePath = __DIR__ . '/data';

        $this->contactSchema = [
            "message" => [
                "validators" => [
                    "required" => [
                        "message" => "Please enter a message"
                    ]
                ]
            ]
        ];
    }

    public function testReadJsonSchema()
    {
        // Arrange
        $schema = new RequestSchema($this->basePath . '/contact.json');

        // Act
        $result = $schema->all();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }

    public function testReadYamlSchema()
    {
        // Arrange
        $schema = new RequestSchema($this->basePath . '/contact.yaml');

        // Act
        $result = $schema->all();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }

    public function testSetDefault()
    {
        // Arrange
        $schema = new RequestSchema($this->basePath . '/contact.yaml');

        // Act
        $schema->setDefault('message', "I require more voles.");
        $result = $schema->all();

        // Assert
        $contactSchema = [
            "message" => [
                "default" => "I require more voles.",
                "validators" => [
                    "required" => [
                        "message" => "Please enter a message"
                    ]
                ]
            ]
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testAddValidator()
    {
        // Arrange
        $schema = new RequestSchema($this->basePath . '/contact.yaml');

        // Act
        $schema->addValidator('message', 'length', [
            'max' => 10000,
            'message' => 'Your message is too long!'
        ]);
        $result = $schema->all();

        // Assert
        $contactSchema = [
            "message" => [
                "validators" => [
                    "required" => [
                        "message" => "Please enter a message"
                    ],
                    "length" => [
                        "max" => 10000,
                        "message" => "Your message is too long!"
                    ]
                ]
            ]
        ];
        $this->assertArraySubset($contactSchema, $result);
    }

    public function testSetTransformation()
    {
        // Arrange
        $schema = new RequestSchema($this->basePath . '/contact.yaml');

        // Act
        $schema->setTransformations('message', ['purge', 'owlify']);
        $result = $schema->all();

        // Assert
        $contactSchema = [
            "message" => [
                "validators" => [
                    "required" => [
                        "message" => "Please enter a message"
                    ]
                ],
                "transformations" => [
                    "purge",
                    "owlify"
                ]
            ]
        ];
        $this->assertArraySubset($contactSchema, $result);
    }
}
