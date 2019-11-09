<?php

/*
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Adapter;

use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;
use UserFrosting\I18n\Translator;

/**
 * ClientSideValidationAdapter Class.
 *
 * Loads validation rules from a schema and generates client-side rules compatible with a particular client-side (usually Javascript) plugin.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
abstract class ClientSideValidationAdapter
{
    /**
     * @var RequestSchemaInterface
     */
    protected $schema;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Create a new client-side validator.
     *
     * @param RequestSchemaInterface $schema     A RequestSchema object, containing the validation rules.
     * @param Translator             $translator A Translator to be used to translate message ids found in the schema.
     */
    public function __construct(RequestSchemaInterface $schema, Translator $translator)
    {
        // Set schema
        $this->setSchema($schema);

        // Set translator
        $this->setTranslator($translator);
    }

    /**
     * Set the schema for this validator.
     *
     * @param RequestSchemaInterface $schema A RequestSchemaInterface object, containing the validation rules.
     */
    public function setSchema(RequestSchemaInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Set the translator for this validator, as a valid MessageTranslator object.
     *
     * @param Translator $translator A Translator to be used to translate message ids found in the schema.
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Generate and return the validation rules for this specific validation adapter.
     *
     * This method returns a collection of rules, in the format required by the specified plugin.
     *
     * @param string $format       The format in which to return the rules.  For example, "json" or "html5".
     * @param bool   $stringEncode In the case of JSON rules, specify whether or not to encode the result as a serialized JSON string.
     *
     * @return mixed The validation rule collection.
     */
    abstract public function rules($format = 'json', $stringEncode = true);
}
