<?php

require_once("fortress/config-fortress.php");

// Load the request schema
$requestSchema = new Fortress\RequestSchema("fortress/schema/forms/philosophers.json");

// Expect a POST request
$rf = new Fortress\HTTPRequestFortress("get", $requestSchema, "index");
// Remove ajaxMode and csrf_token from the request data
$rf->removeFields(['ajaxMode', 'csrf_token']);

// Sanitize and validate data, halting on errors
$rf->sanitize();

echo "Sanitized data: <br>";
print_r($rf->data());

$rf->validate();

// Create a new group with the filtered data
$data = $rf->data();

if (!yourFunctionHere($data)){
    $rf->raiseFatalError();
}

// If we've made it this far, success!
$rf->raiseSuccess();

?>