<?php
/**
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\RequestSchema;

/**
 * Represents a schema for an HTTP request, compliant with the WDVSS standard (https://github.com/alexweissman/wdvss)
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
interface RequestSchemaInterface
{
    /**
     * Get all items in the schema.
     *
     * @return array
     */
    public function all();

    /**
     * Recursively merge values (scalar or array) into this repository.
     *
     * If no key is specified, the items will be merged in starting from the top level of the array.
     * If a key IS specified, items will be merged into that key.
     * Nested keys may be specified using dot syntax.
     * @param string|null $key
     * @param mixed       $items
     */
    public function mergeItems($key, $items);

    /**
     * Set the default value for a specified field.
     *
     * If the specified field does not exist in the schema, add it.  If a default already exists for this field, replace it with the value specified here.
     * @param  string                 $field The name of the field (e.g., "user_name")
     * @param  string                 $value The new default value for this field.
     * @return RequestSchemaInterface This schema object.
     */
    public function setDefault($field, $value);

    /**
     * Adds a new validator for a specified field.
     *
     * If the specified field does not exist in the schema, add it.  If a validator with the specified name already exists for the field,
     * replace it with the parameters specified here.
     * @param  string                 $field         The name of the field for this validator (e.g., "user_name")
     * @param  string                 $validatorName A validator rule, as specified in https://github.com/alexweissman/wdvss (e.g. "length")
     * @param  array                  $parameters    An array of parameters, hashed as parameter_name => parameter value (e.g. [ "min" => 50 ])
     * @return RequestSchemaInterface This schema object.
     */
    public function addValidator($field, $validatorName, array $parameters = []);

    /**
     * Remove a validator for a specified field.
     *
     * @param  string                 $field         The name of the field for this validator (e.g., "user_name")
     * @param  string                 $validatorName A validator rule, as specified in https://github.com/alexweissman/wdvss (e.g. "length")
     * @return RequestSchemaInterface This schema object.
     */
    public function removeValidator($field, $validatorName);

    /**
     * Set a sequence of transformations for a specified field.
     *
     * If the specified field does not exist in the schema, add it.
     * @param  string                 $field           The name of the field for this transformation (e.g., "user_name")
     * @param  string|array           $transformations An array of transformations, as specified in https://github.com/alexweissman/wdvss (e.g. "purge")
     * @return RequestSchemaInterface This schema object.
     */
    public function setTransformations($field, $transformations = []);
}
