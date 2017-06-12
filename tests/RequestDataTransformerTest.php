<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\Support\Repository\Loader\YamlFileLoader;

class RequestDataTransformerTest extends TestCase
{
    protected $basePath;

    protected $transformer;
    
    public function setUp()
    {
        $this->basePath = __DIR__ . '/data';

        // Arrange
        $loader = new YamlFileLoader($this->basePath . '/register.yaml');
        $schema = new RequestSchemaRepository($loader->load());
        $this->transformer = new RequestDataTransformer($schema);
    }

    /**
     * Basic whitelisting
     */
    public function testBasic()
    {
        // Arrange
        $rawInput = [
            'email' => 'david@owlfancy.com',
            'admin' => 1
        ];

        // Arrange
        $schema = new RequestSchemaRepository();

        $schema->mergeItems(null, [
            'email' => []
        ]);
        $this->transformer = new RequestDataTransformer($schema);
        
        // Act
        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email' => 'david@owlfancy.com'
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Trim" transformer
     */
    public function testTrim()
    {
        // Act
        $rawInput = [
            'display_name' => "THE GREATEST  "
        ];
        
        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email' => 'david@owlfancy.com',
            'display_name' => "THE GREATEST"
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Escape" transformer
     */
    public function testEscape()
    {
        // Act
        $rawInput = [
            'display_name' => "<b>My Super-Important Name</b>"
        ];
        
        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email' => 'david@owlfancy.com',
            'display_name' => "&#60;b&#62;My Super-Important Name&#60;/b&#62;"
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Purge" transformer
     */
    public function testPurge()
    {
        // Act
        $rawInput = [
            'user_name' => "<b>My Super-Important Name</b>"
        ];
        
        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email' => 'david@owlfancy.com',
            'user_name' => "My Super-Important Name"
        ];

        $this->assertEquals($transformedData, $result);
    }

    /**
     * "Purify" transformer
     */
    public function testPurify()
    {
        // Act
        $rawInput = [
            'puppies' => "<script>I'm definitely really a puppy  </script><b>0</b>"
        ];
        
        $result = $this->transformer->transform($rawInput, 'skip');

        // Assert
        $transformedData = [
            'email' => 'david@owlfancy.com',
            'puppies' => "<b>0</b>"
        ];

        $this->assertEquals($transformedData, $result);
    }
}
