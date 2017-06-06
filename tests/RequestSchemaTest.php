<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Fortress\Schema\JsonSchema;
use UserFrosting\Fortress\Schema\YamlSchema;

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
        $schema = new JsonSchema($this->basePath . '/contact.json');

        // Act
        $result = $schema->getSchema();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }

    public function testReadYamlSchema()
    {
        // Arrange
        $schema = new YamlSchema($this->basePath . '/contact.yaml');

        // Act
        $result = $schema->getSchema();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }

    public function testReadYamlFromJsonSchema()
    {
        // Arrange
        $schema = new YamlSchema($this->basePath . '/contact.json');

        // Act
        $result = $schema->getSchema();

        // Assert
        $this->assertArraySubset($this->contactSchema, $result);
    }
}
