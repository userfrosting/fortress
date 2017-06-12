<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @license   https://github.com/userfrosting/fortress/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Fortress\RequestSchema;

use UserFrosting\Support\Repository\Repository;

/**
 * Represents a schema for an HTTP request, compliant with the WDVSS standard (https://github.com/alexweissman/wdvss)
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class RequestSchemaRepository extends Repository
{
    /**
     * Set the default value for a specified field.
     *
     * If the specified field does not exist in the schema, add it.  If a default already exists for this field, replace it with the value specified here.
     * @param string $field The name of the field (e.g., "user_name")
     * @param string $value The new default value for this field.
     * @return RequestSchema This schema object.
     */
    public function setDefault($field, $value)
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        $this->items[$field]['default'] = $value;

        return $this;
    }

    /**
     * Adds a new validator for a specified field.
     *
     * If the specified field does not exist in the schema, add it.  If a validator with the specified name already exists for the field,
     * replace it with the parameters specified here.
     * @param string $field The name of the field for this validator (e.g., "user_name")
     * @param string $validatorName A validator rule, as specified in https://github.com/alexweissman/wdvss (e.g. "length")
     * @param array $parameters An array of parameters, hashed as parameter_name => parameter value (e.g. [ "min" => 50 ])
     * @return RequestSchema This schema object.
     */
    public function addValidator($field, $validatorName, $parameters = [])
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        if (!isset($this->items[$field]['validators'])) {
            $this->items[$field]['validators'] = [];
        }

        $this->items[$field]['validators'][$validatorName] = $parameters;

        return $this;
    }

    /**
     * Set a sequence of transformations for a specified field.
     *
     * If the specified field does not exist in the schema, add it.
     * @param string $field The name of the field for this transformation (e.g., "user_name")
     * @param string[] $transformations An array of transformations, as specified in https://github.com/alexweissman/wdvss (e.g. "purge")
     * @return RequestSchema This schema object.
     */
    public function setTransformations($field, $transformations = [])
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        $this->items[$field]['transformations'] = $transformations;

        return $this;
    }
}
