<?php

//set_error_handler('logAllErrors');
require_once("fortress/config-fortress.php");

// Load the request schema
$requestSchema = new Fortress\RequestSchema("fortress/schema/forms/philosophers.json");

// Expect a POST request
$rf = new Fortress\HTTPRequestFortress("get", $requestSchema, "index");
// Remove ajaxMode and csrf_token from the request data
$rf->removeFields(['ajaxMode', 'csrf_token']);

// Determine whether this is an AJAX request
$ajax = $rf->getAjaxMode();

// User must be logged in
//checkLoggedInUser($ajax);

// Sanitize and validate data, halting on errors
$rf->sanitize();
$rf->validate();

// Create a new group with the filtered data
$data = $rf->data();
if (!createGroup($data)){
    $rf->raiseFatalError();
}

//restore_error_handler();

// If we've made it this far, success!
$rf->raiseSuccess();

?>