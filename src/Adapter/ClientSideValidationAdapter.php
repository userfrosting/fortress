<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Adapter;

use UserFrosting\Fortress\RequestSchema\RequestSchemaInterface;
use UserFrosting\I18n\MessageTranslator;

/**
 * ClientSideValidationAdapter Class
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
     * @var MessageTranslator
     */
    protected $translator;

    /**
     * Create a new client-side validator.
     *
     * @param RequestSchemaInterface $schema     A RequestSchema object, containing the validation rules.
     * @param MessageTranslator      $translator A MessageTranslator to be used to translate message ids found in the schema.
     */
    public function __construct(RequestSchemaInterface $schema, MessageTranslator $translator)
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
     * @param MessageTranslator $translator A MessageTranslator to be used to translate message ids found in the schema.
     */
    public function setTranslator(MessageTranslator $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Generate and return the validation rules for this specific validation adapter.
     *
     * This method returns a collection of rules, in the format required by the specified plugin.
     * @param  string $format       The format in which to return the rules.  For example, "json" or "html5".
     * @param  bool   $stringEncode In the case of JSON rules, specify whether or not to encode the result as a serialized JSON string.
     * @return mixed  The validation rule collection.
     */
    abstract public function rules($format = 'json', $stringEncode = true);
}
