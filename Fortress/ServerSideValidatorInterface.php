<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2017 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/licenses/UserFrosting.md (MIT License)
 */

namespace UserFrosting\Fortress;

/**
 * ServerSideValidator Interface
 *
 * Loads validation rules from a schema and validates a target array of data.
 *
 * @author Alexander Weissman
 * @link https://alexanderweissman.com
 */
interface ServerSideValidatorInterface
{
    /**
     * Set the schema for this validator, as a valid RequestSchema object.
     *
     * @param RequestSchema $schema A RequestSchema object, containing the validation rules.
     */
    public function setSchema($schema);

    /**
     * Set the translator for this validator, as a valid MessageTranslator object.
     *
     * @param MessageTranslator $translator A MessageTranslator to be used to translate message ids found in the schema.
     */
    public function setTranslator($schema);

    /**
     * Validate the specified data against the schema rules.
     *
     * @param array $data An array of data, mapping field names to field values.
     * @return boolean True if the data was successfully validated, false otherwise.
     */
    public function validate($data);

    public function data();

    public function errors();
}