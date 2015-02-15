<?php

require_once("fortress/config-fortress.php");

// Set the message stream (should be done in config file)
session_start();
Fortress\HTTPRequestFortress::setMessageStream('userAlerts');

// Load the request schema
$requestSchema = new Fortress\RequestSchema("fortress/schema/forms/philosophers.json");

// Expect a POST request
$rf = new Fortress\HTTPRequestFortress("get", $requestSchema, "index");
// Remove ajaxMode and csrf_token from the request data
$rf->removeFields(['ajaxMode', 'csrf_token']);

// Sanitize, and print sanitized data for demo purposes
$rf->sanitize();

echo "Sanitized data: <br>";

print_r($rf->data());


// Validate.  In normal usage we'd want the script to simply halt on validation errors.  But for this demo, we will simply print the message stream.
if (!$rf->validate(true, false)) {
    // Test the error stream and reset
    echo "<pre>";
    print_r($_SESSION['Fortress']['userAlerts']);
    echo "</pre>";
    Fortress\HTTPRequestFortress::resetMessageStream();
}

// Test client validators
$clientVal = new Fortress\ClientSideValidator("fortress/schema/forms/philosophers.json");
echo "<pre>";
print_r($clientVal->formValidationRulesJson());
echo "</pre>";

// Create a new group with the filtered data
$data = $rf->data();

if (!yourFunctionHere($data)){
    $rf->raiseFatalError();
}

// If we've made it this far, success!
$rf->raiseSuccess();



?>