<?php

namespace Fortress;

/**
 * ClientSideValidationAdapter Class
 *
 * Loads validation rules from a schema and generates client-side rules compatible with a particular client-side (usually Javascript) plugin.
 *
 * @package Fortress
 * @author Alex Weissman
 * @link http://alexanderweissman.com
 */
abstract class ClientSideValidationAdapter {

    /**
     * @var RequestSchema
     */
    protected $_schema;

    /**
     * @var MessageTranslator
     */    
    protected $_translator; 

    /**
     * Create a new client-side validator.
     *
     * @param RequestSchema $schema A RequestSchema object, containing the validation rules.
     * @param MessageTranslator $translator A MessageTranslator to be used to translate message ids found in the schema.
     */  
    public function __construct($schema, $translator) {        
        // Set schema
        $this->setSchema($schema);
        
        // Set translator
        $this->_translator = $translator;
    }
    
    /**
     * Set the schema for this validator, as a valid RequestSchema object.
     *
     * @param RequestSchema $schema A RequestSchema object, containing the validation rules.
     */
    public function setSchema($schema){
        $this->_schema = $schema;
    }
    
}
