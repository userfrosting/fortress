<?php

namespace Fortress;

/*
 * A server-side filter for HTTP requests.  Sanitizes and validates GET and POST data, generates error messages, adds error messages to an error stream, and redirects and/or returns a JSON error object.
 *
 */

class HTTPRequestFortress {

    protected $_validator;                // A valid ServerSideValidatorInterface object
    protected $_sanitizer;                // A valid DataSanitizerInterface object
    protected $_message_stream;           // A message stream (array)

    protected $_request_method = "post";  // "get" or "post"
    protected $_followup_uri = null;      // A URI to redirect to when the request is finished being processed, either on error or success
    protected $_ajax = false;             // true if this is an AJAX request, false otherwise
    
    protected $_data = [];                // Gets set to the POST or GET request data
    protected $_schema;                   // A valid RequestSchema object
    
    public function __construct($request_method = "post", $schema = null, $followup_uri = null, $locale = "en_US") {
        // Set the schema
        $this->setSchema($schema);
        
        // Set the followup URI.
        $this->setFollowupURI($followup_uri);
        
        // Set the request method
        $request_method = strtolower($request_method);
        if (in_array($request_method, ["post", "get"]))
            $this->_request_method = $request_method;
        else
            throw new \Exception("$request_method must be 'get' or 'post'.");
    
        // Check that the submitted request method matches the parameter request method.
        if ($_SERVER['REQUEST_METHOD'] != strtoupper($this->_request_method)) {
            $this->addMessage("danger", "Invalid request method: request method must be '{$this->_request_method}'.");
            $this->raiseFatalError();
        }
        
        // Set data array
        if ($request_method == "post")
            $this->_data = $_POST;
        else
            $this->_data = $_GET;
            
        // Determine whether this request is an ajax request, based on the `ajaxMode` parameter
        if (isset($this->_data['ajaxMode']) and $this->_data['ajaxMode'] == "true" ){
            $this->_ajax = true;
        } else {
            $this->_ajax = false;
        }
    
        // Construct default sanitizer and validators
        $this->_sanitizer = new DataSanitizer($schema);
        $this->_validator = new ServerSideValidator($schema, $locale);
        
        // Set up the message stream
        $this->setMessageStream($_SESSION['userAlerts']);   
    }
    
    /* Remove the specified fields from the request data. */
    public function removeFields($fields){
        foreach ($fields as $idx => $field){
            unset($this->_data[$field]);
        }
    }
    
    // Set the ServerSideValidatorInterface instance.
    public function setValidator($validator) {
        if ($validator instanceof ServerSideValidatorInterface)
            $this->_validator = $validator;
        else
            throw new \Exception("$validator must be a valid instance of ServerSideValidatorInterface.");
    }
    
    public function setSchema($schema){
        return $this->_schema = $schema;
    }
    
    /* For non-ajax requests, automatically redirect to this URI after completion */
    public function setFollowupURI($uri){
        return $this->_followup_uri = $uri;
    }
    
    /* Set a message stream, usually a global or session variable. */
    public function setMessageStream(&$stream){
        if (isset($stream)){
            $this->_message_stream = $stream;
        } else {
            $this->_message_stream = [];
        }
    }
    
    // Get the AJAX request mode.
    public function getAjaxMode(){
        return $this->_ajax;
    }    

    /* Get the data for this request, in its current state. */
    public function data(){
        return $this->_data;
    }
    
    /* Sanitize all fields and optionally add any error messages to the global message stream. */
    public function sanitize($reportErrors = true){
        $this->_data = $this->_sanitizer->sanitize($this->_data);
        // TODO: Implement sanitizer errors
    }
    
    /* Validate all fields and optionally add any error messages to the global message stream. */
    public function validate($reportErrors = true){
        $this->_validator->validate($this->_data); 
        if ($reportErrors) {
            if (count($this->_validator->errors()) > 0) {	
                foreach ($this->_validator->errors() as $idx => $field){
                    foreach($field as $eidx => $error) {
                        $this->addMessage("danger", $error);
                    }
                }
            }
        }
        if (count($this->_validator->errors()) > 0) {
            $this->raiseFatalError();
        }
        return $this;
    }    

    // Raise a fatal error, performing appropriate action and halting the script
    public function raiseFatalError() {
        if ($this->_ajax) {
            echo json_encode(array("errors" => 1, "successes" => 0));
        } else {      
            if ($this->_followup_uri != null) {
                header('Location: ' . $this->_followup_uri);
            }
        }
        exit();  
    }

    // Raise a success, rperforming appropriate action and halting the script 
    public function raiseSuccess(){
        if ($this->_ajax) {
          echo json_encode(array("errors" => 0, "successes" => 1));
        } else {
            if ($this->_followup_uri != null) {
                header('Location: ' . $this->_followup_uri);
            }
        }
        exit();    
    }
        
    // Add a session message to the session message stream
    public function addMessage($type, $message){
        error_log($message);
        $alert = array();
        $alert['type'] = $type;
        $alert['message'] = $message;
        $this->_message_stream[] = $alert;
    }
}
    
?>