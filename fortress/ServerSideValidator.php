<?php

namespace Fortress;

interface ServerSideValidatorInterface {
    public function setSchema($schema);
    public function validate($data, $schemaRequired);
    public function data();
    public function errors();    
}

/* Loads validation rules from a schema and validates a target array of data.
*/
class ServerSideValidator extends \Valitron\Validator implements ServerSideValidatorInterface {

    protected $_schema;         // A valid RequestSchema object
    protected $_locale = "";
    
    public function __construct($schema, $locale = "en_US") {        
        // Set schema
        $this->setSchema($schema);
        $this->_locale = $locale;  
        // TODO: use locale to determine Valitron language
        
        // Construct the parent with an empty data array.
        parent::__construct([]);
    }
    
    /* Set the schema for this validator, as a valid RequestSchema object. */
    public function setSchema($schema){
        $this->_schema = $schema;
    }
    
    /* Validate the specified data against the schema rules. */
    public function validate($data, $schemaRequired = true){
        $this->_fields = $data;         // Setting the parent class Validator's field data.
        $this->generateSchemaRules();   // Build Validator rules from the schema.
        return parent::validate();      // Validate!
    }
    
    /* Generate and add rules from the schema */
    private function generateSchemaRules(){
        foreach ($this->_schema->getSchema() as $field_name => $field){
            $validators = $field['validators'];
            foreach ($validators as $validator_name => $validator){
                // Required validator
                if ($validator_name == "required"){
                    $this->rule("required", $field_name);
                }
                // String length validator
                if ($validator_name == "length"){
                    if (isset($validator['min']) && isset($validator['max'])) {
                        $this->rule("lengthBetween", $field_name, $validator['min'], $validator['max']);
                    } else {          
                        if (isset($validator['min']))
                            $this->rule("lengthMin", $field_name, $validator['min']);
                        if (isset($validator['max']))
                            $this->rule("lengthMax", $field_name, $validator['max']);
                    }
                }
                // Numeric range validator
                if ($validator_name == "range"){
                    if (isset($validator['min'])){
                        $this->rule("min", $field_name, $validator['min']);
                    }               
                    if (isset($validator['max'])){
                        $this->rule("max", $field_name, $validator['max']);
                    }
                }
                // Integer validator
                if ($validator_name == "integer"){
                    $this->rule("integer", $field_name); 
                }                  
                // Choice validator
                if ($validator_name == "choice"){
                    // For now, just check that it is an array.  Really we need a new validation rule here.
                    $this->rule("array", $field_name);
                }
                // Email validator
                if ($validator_name == "email"){
                    $this->rule("email", $field_name);
                }            
                // Equals validator
                if ($validator_name == "equals"){
                    $this->rule("equals", $field_name, $validator['field']);
                }
            }
        }
    }    
}

?>