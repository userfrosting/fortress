<?php

namespace Fortress;

/**
 * RequestSchema Class
 *
 * Represents a schema for an HTTP request, compliant with the WDVSS standard (https://github.com/alexweissman/wdvss)
 *
 * @package Fortress
 * @author Alex Weissman
 * @link http://alexanderweissman.com
 */
class RequestSchema {

    /**
     * @var array The schema, as a dictionary of field names -> field properties
     */
    protected $_schema = [];

    /**
     * Loads the request schema from a file.
     *
     * @param string $input The full path to the file or associative Array containing the 
     * [WDVSS schema](https://github.com/alexweissman/wdvss).
     * @throws Exception Not an Array or File Path (string), The file does not exist or is not a valid JSON schema.
     */
    public function __construct($input) {
        $inputtype = gettype($input);
        if ($inputtype == 'array') {
            $this->_schema = $input;
        } else if ($inputtype == 'string') {
            if (file_exists($input)) {
                $this->_schema = json_decode(file_get_contents($input), true);
                if ($this->_schema === null) {
                    throw new \Exception("The file ($input) does not contain a valid JSON : " . json_last_error());
                }
            } else {
                throw new \Exception("The schema file ($input) could not be found");
            }
        } else {
            throw new \Exception("Invalid input format");
        }
    }

    /**
     * Adds the request schema from a file or Array.
     *
     * @param string $input The Array or full path to the file containing the [WDVSS schema](https://github.com/alexweissman/wdvss).
     * @throws Exception The input is not an Array or String, file does not exist or is not a valid JSON schema.
     */
    public function addSchema($input) {
        $inputtype = gettype($input);
        if ($inputtype == 'array') {
            $this->_schema = array_merge($this->_schema, $input);
        } else if ($inputtype == 'string') {
            if (file_exists($input)) {
                $var_newinput = json_decode(file_get_contents($input), true);
                if($var_newinput === null) {
                    throw new \Exception("The file ($input) does not contain a valid JSON : " . json_last_error());
                }
                $this->_schema = array_merge($this->_schema, $var_newinput);
            } else {
                throw new \Exception("The schema file ($input) could not be found");
            }
        } else {
            throw new \Exception("Invalid input format");
        }
    }
    
    /**
     * Get the schema, as an associative array.
     *
     * @return array The schema data.
     */
    public function getSchema() {
        return $this->_schema;
    }

    /**
     * Set the default value for a specified field.  
     *
     * If the specified field does not exist in the schema, add it.  If a default already exists for this field, replace it with the value specified here.
     * @param string $field The name of the field (e.g., "user_name")
     * @param string $value The new default value for this field.
     * @return RequestSchema This schema object.
     */
    public function setDefault($field, $value) {
        if (!isset($this->_schema[$field]))
            $this->_schema[$field] = [];
        $this->_schema[$field]['default'] = $value;

        return $this;
    }

    /**
     * Adds a new validator for a specified field.  
     *
     * If the specified field does not exist in the schema, add it.  If a validator with the specified name already exists for the field,
     * replace it with the parameters specified here.
     * @param string $field The name of the field for this validator (e.g., "user_name")
     * @param string $validator_name A validator rule, as specified in https://github.com/alexweissman/wdvss (e.g. "length")
     * @param array $parameters An array of parameters, hashed as parameter_name => parameter value (e.g. [ "min" => 50 ])
     * @return RequestSchema This schema object.
     */
    public function addValidator($field, $validator_name, $parameters = []) {
        if (!isset($this->_schema[$field]))
            $this->_schema[$field] = [];
        if (!isset($this->_schema[$field]['validators']))
            $this->_schema[$field]['validators'] = [];
        $this->_schema[$field]['validators'][$validator_name] = $parameters;

        return $this;
    }

    /**
     * Adds a new sanitizer for a specified field.  
     *
     * If the specified field does not exist in the schema, add it.  If a sanitizer with the specified name already exists for the field,
     * replace it with the parameters specified here.
     * @param string $field The name of the field for this sanitizer (e.g., "user_name")
     * @param string $sanitizer_name A sanitizer rule, as specified in https://github.com/alexweissman/wdvss (e.g. "purge")
     * @param array $parameters An array of parameters, hashed as parameter_name => parameter value
     * @return RequestSchema This schema object.
     */
    public function addSanitizer($field, $sanitizer_name, $parameters = []) {
        if (!isset($this->_schema[$field]))
            $this->_schema[$field] = [];
        if (!isset($this->_schema[$field]['sanitizers']))
            $this->_schema[$field]['sanitizers'] = [];
        $this->_schema[$field]['sanitizers'][$sanitizer_name] = $parameters;

        return $this;
    }

}
