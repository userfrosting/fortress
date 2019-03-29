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
use UserFrosting\Fortress\RequestSchema;

class RequestSchemaTest extends TestCase
{
    public function setUp()
    {
        $this->basePath = __DIR__.'/data/contact.json';

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

    public function testWithNoPath()
    {
        $requestSchema = new RequestSchema();
        $this->assertSame([], $requestSchema->getSchema());
        $this->assertSame($requestSchema->all(), $requestSchema->getSchema());
    }

    public function testWithPath()
    {
        $requestSchema = new RequestSchema($this->basePath);
        $this->assertArraySubset($this->contactSchema, $requestSchema->getSchema());
        $this->assertSame($requestSchema->all(), $requestSchema->getSchema());
    }
}
