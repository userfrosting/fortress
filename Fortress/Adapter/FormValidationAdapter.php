<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2017 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Fortress\Adapter;

/**
 * FormValidationAdapter Class
 *
 * Loads validation rules from a schema and generates client-side rules compatible with the [FormValidation](http://formvalidation.io) JS plugin.
 *
 * @author Alex Weissman
 * @link https://alexanderweissman.com
 */
class FormValidationAdapter extends ClientSideValidationAdapter
{
    /**
     * {@inheritDoc}
     */
    public function rules($format = "json", $stringEncode = true)
    {
        if ($format == "html5") {
            return $this->formValidationRulesHtml5();
        } else {
            return $this->formValidationRulesJson($stringEncode);
        }
    }

    /**
     * Generate FormValidation compatible rules from the specified RequestSchema, as a JSON document.
     * See [this](http://formvalidation.io/getting-started/#calling-plugin) as an example of what this function will generate.
     *
     * @param boolean $encode Specify whether to return a PHP array, or a JSON-encoded string.
     * @return string|array Returns either the array of rules, or a JSON-encoded representation of that array.
     */
    public function formValidationRulesJson($encode = true)
    {
        $clientRules = [];
        $implicitRules = [];
        foreach ($this->schema->getSchema() as $fieldName => $field) {
            $clientRules[$fieldName] = [];
            $clientRules[$fieldName]['validators'] = [];

            if (isset($field['validators'])) {
                $validators = $field['validators'];
                foreach ($validators as $validatorName => $validator) {
                    $clientRules[$fieldName]['validators'] = array_merge($clientRules[$fieldName]['validators'], $this->transformValidator($fieldName, $validatorName, $validator));
                }
            }
        }
        if ($encode) {
            return json_encode($clientRules, JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);
        } else {
            return $clientRules;
        }
    }

    /**
     * Generate FormValidation compatible rules from the specified RequestSchema, as HTML5 `data-*` attributes.
     * See [Setting validator options via HTML attributes](http://formvalidation.io/examples/attribute/) as an example of what this function will generate.
     *
     * @return array Returns an array of rules, mapping field names -> string of data-* attributes, separated by spaces.
     * Example: `data-fv-notempty data-fv-notempty-message="The gender is required"`.
     */
    public function formValidationRulesHtml5()
    {
        $clientRules = array();
        $implicitRules = array();
        foreach ($this->schema->getSchema() as $fieldName => $field) {
            $fieldRules = "";
            $validators = $field['validators'];

            foreach ($validators as $validatorName => $validator) {
                // Skip messages that are for server-side use only
                if (isset($validator['domain']) && $validator['domain'] == "server") {
                    continue;
                }

                // Required validator
                if ($validatorName == "required") {
                    $prefix = "data-fv-notempty";
                    $fieldRules .= $this->html5Attributes($validator, $prefix);
                }
                // String length validator
                if ($validatorName == "length"){
                    $prefix = "data-fv-stringlength";
                    $fieldRules .= $this->html5Attributes($validator, $prefix);
                    if (isset($validator['min'])) {
                        $fieldRules .= "$prefix-min={$validator['min']} ";
                    }
                    if (isset($validator['max'])) {
                        $fieldRules .= "$prefix-max={$validator['max']} ";
                    }
                }
                // Numeric range validator
                if ($validatorName == "range") {
                    if (isset($validator['min']) && isset($validator['max'])) {
                        $prefix = "data-fv-between";
                        $fieldRules .= $this->html5Attributes($validator, $prefix);
                        $fieldRules .= "$prefix-min={$validator['min']} ";
                        $fieldRules .= "$prefix-max={$validator['max']} ";
                    } else {
                        if (isset($validator['min'])) {
                            $prefix = "data-fv-greaterthan";
                            $fieldRules .= $this->html5Attributes($validator, $prefix);
                            $fieldRules .= "$prefix-value={$validator['min']} ";
                        }

                        if (isset($validator['max'])) {
                           $prefix = "data-fv-lessthan";
                            $fieldRules .= $this->html5Attributes($validator, $prefix);
                            $fieldRules .= "$prefix-value={$validator['max']} ";
                        }
                    }
                }
                // Integer validator
                if ($validatorName == "integer") {
                    $prefix = "data-fv-integer";
                    $fieldRules .= $this->html5Attributes($validator, $prefix);
                }
                // Array validator
                if ($validatorName == "array") {
                    $prefix = "data-fv-choice";
                    $fieldRules .= $this->html5Attributes($validator, $prefix);
                    if (isset($validator['min'])) {
                        $fieldRules .= "$prefix-min={$validator['min']} ";
                    }
                    if (isset($validator['max'])) {
                        $fieldRules .= "$prefix-max={$validator['max']} ";
                    }
                }
                // Email validator
                if ($validatorName == "email") {
                    $prefix = "data-fv-emailaddress";
                    $fieldRules .= $this->html5Attributes($validator, $prefix);
                }
                // Match another field
                if ($validatorName == "matches") {
                    $prefix = "data-fv-identical";
                    if (isset($validator['field'])) {
                        $fieldRules .= "$prefix-field={$validator['field']} ";
                    } else {
                        return null;    // TODO: throw exception
                    }

                    $fieldRules = $this->html5Attributes($validator, $prefix);
                    // Generates validator for matched field
                    $implicitRules[$validator['field']] = $fieldRules;
                    $implicitRules[$validator['field']] .= "$prefix-field=$fieldName ";
                }
            }

            $clientRules[$fieldName] = $fieldRules;
        }

        // Merge in any implicit rules
        foreach ($implicitRules as $fieldName => $field) {
            $clientRules[$fieldName] .= $field;
        }

        return $clientRules;
    }

    /**
     * Transform a validator for a particular field into one or more FormValidation rules.
     *
     * @param string $fieldName
     * @param string $validatorName
     * @param string[] $validator
     */
    private function transformValidator($fieldName, $validatorName, $validator)
    {
        $params = [];
        // Message
        if (isset($validator['message'])) {
            $validator = array_merge(["self" => $fieldName], $validator);
            $params["message"] = $this->translator->translate($validator['message'], $validator);
        }
        $transformedValidatorJson = [];

        switch ($validatorName) {
            // Required validator
            case "required":
                $transformedValidatorJson['notEmpty'] = $params;
                break;
            case "length":
                if (isset($validator['min'])) {
                    $params['min'] = $validator['min'];
                }
                if (isset($validator['max'])) {
                    $params['max'] = $validator['max'];
                }
                $transformedValidatorJson['stringLength'] = $params;
                break;
            case "integer":
                $transformedValidatorJson['integer'] = $params;
                break;
            case "numeric":
                $transformedValidatorJson['numeric'] = $params;
                break;
            case "range":
                if (isset($validator['min'])) {
                    $params['min'] = $validator['min'];
                }
                if (isset($validator['max'])) {
                    $params['max'] = $validator['max'];
                }
                if (isset($validator['min']) && isset($validator['max'])) {
                    $transformedValidatorJson['between'] = $params;
                } elseif (isset($validator['min'])) {
                    $transformedValidatorJson['greaterThan'] = $params;
                } elseif (isset($validator['max'])) {
                    $transformedValidatorJson['lessThan'] = $params;
                }
                break;
            case "array":
                if (isset($validator['min'])) {
                    $params['min'] = $validator['min'];
                }
                if (isset($validator['max'])) {
                    $params['max'] = $validator['max'];
                }
                $transformedValidatorJson['choice'] = $params;
                break;
            case "email":
                $transformedValidatorJson['emailAddress'] = $params;
                break;
            case "matches":
                if (isset($validator['field'])) {
                    $params['field'] = $validator['field'];
                }
                $transformedValidatorJson['identical'] = $params;
                break;
            case "not_matches":
                if (isset($validator['field'])) {
                    $params['field'] = $validator['field'];
                }
                $transformedValidatorJson['different'] = $params;
                break;
            case "member_of":
                if (isset($validator['values'])) {
                    $params['regexp'] = "^" . implode("|", $validator['values']) . "$";
                }
                $transformedValidatorJson['regexp'] = $params;
                break;
            case "not_member_of":
                if (isset($validator['values'])) {
                    $params['regexp'] = "^(?!" . implode("|", $validator['values']) . "$).*$";
                }
                $transformedValidatorJson['regexp'] = $params;
                break;
            default:
                break;
        }
        return $transformedValidatorJson;
    }

    /**
     * Transform a validator for a particular field into a string of FormValidation rules as HTML data-* attributes.
     *
     * @param string[] $validator
     * @param string $prefix
     */
    public function html5Attributes($validator, $prefix)
    {
        $attr = "$prefix=true ";
        if (isset($validator['message'])) {
            $msg = "";
            if (isset($validator['message'])) {
                $msg = $validator['message'];
            } else {
                return $attr;
            }
            $attr .= "$prefix-message=\"$msg\" ";
        }
        return $attr;
    }
}
