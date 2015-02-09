<?php

namespace Fortress;

interface DataSanitizerInterface {
    public function setSchema($schema);
    public function sanitize($data, $schemaRequired);
    public function data(); 
}

/* Perform sanitization and transformation on a set of data fields, as specified by a RequestSchema. */    
class DataSanitizer implements DataSanitizerInterface {
    protected $_schema;     // A valid RequestSchema object
    protected $_purifier;   // A valid HTMLPurifier object
    protected $_sanitizedData = [];
    
    public function __construct($schema){
        // Create purifier
        $this->_purifier = new \HTMLPurifier();
         
        // Set schema
        $this->setSchema($schema);
    }
    
    /* Set the schema for this sanitizer, as a valid RequestSchema object. */
    public function setSchema($schema){
        $this->_schema = $schema;
    }

    public function data(){
        return $this->_sanitizedData;
    }
    
    /* Perform transformations, in the following order:
     * 1. Set any default values for unspecified fields.
     * 2. Perform any other specified transformations.
     * 3. Escape/purge/purify HTML entities
     */
    public function sanitize($data, $schemaRequired = true) {
        // 1. Get default values for any fields missing from $data.  Primarily for checkboxes, etc which are not submitted when they are unchecked
        
        
        
        
        // 2. Perform transformations on each value in the $data array.  This is important for preventing XSS attacks.
        // If there is a sanitization rule specified in the schema, use that.  Otherwise, apply the FILTER_SANITIZE_SPECIAL_CHARS filter by default.
        $this->_sanitizedData = [];
        foreach ($data as $name => $value){
            $this->_sanitizedData[$name] = $this->sanitizeField($name, $value, $schemaRequired);   
        }
        
        return $this->_sanitizedData;
    }
    
    // Sanitize a raw field value.  If $schemaRequired is set to true, it will also require that the field exists in the schema.
    public function sanitizeField($name, $rawValue, $schemaRequired = true){
        // Default sanitization behavior
        if (!isset($this->_schema->getSchema()[$name])) {
            if ($schemaRequired)
                throw new \Exception("The field '$name' is not a valid input field.");
            else {
                return escapeHtmlCharacters($rawValue);
            }
        }
        
        // Field exists in schema, so validate accordingly
        if (!isset($this->_schema[$name]['sanitizer'])) {
            return $this->escapeHtmlCharacters($rawValue);
        }
        
        switch (strtolower($this->_schema[$name]['sanitizer'])){
            case "purify": return $this->_purifier->purify($rawValue);
            case "escape": 
            default: return $this->escapeHtmlCharacters($rawValue);
        }
    }
    

    /* Sets the default value for a field. */
    public function setDefault($field, $defaultValue) {
        if (!isset($this->_fields[$field])){
            $this->_fields[$field] = $defaultValue;
        }
        return $this;
    }

    /* Autodetect if a field is an array or scalar, and filter appropriately. */
    private function escapeHtmlCharacters($value){
            if (is_array($value))
            return filter_var_array($value, FILTER_SANITIZE_SPECIAL_CHARS);
        else
            return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}

?>
