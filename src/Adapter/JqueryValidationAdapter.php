<?php

/*
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Adapter;

/**
 * JqueryValidationAdapter Class.
 *
 * Loads validation rules from a schema and generates client-side rules compatible with the [jQuery Validation](http://http://jqueryvalidation.org) JS plugin.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class JqueryValidationAdapter extends ClientSideValidationAdapter
{
    /**
     * Generate jQuery Validation compatible rules from the specified RequestSchema, as a JSON document.
     * See [this](https://github.com/jzaefferer/jquery-validation/blob/master/demo/bootstrap/index.html#L168-L209) as an example of what this function will generate.
     *
     * @param bool $stringEncode Specify whether to return a PHP array, or a JSON-encoded string.
     *
     * @return string|array Returns either the array of rules, or a JSON-encoded representation of that array.
     */
    public function rules($format = 'json', $stringEncode = false, $arrayPrefix = '')
    {
        $clientRules = [];
        $clientMessages = [];
        $implicitRules = [];
        $fieldName = '';
        $fieldNameOnly = '';
        foreach ($this->schema->all() as $fieldNameO => $field) {
            $fieldNameOnly = $fieldNameO;
            if ($arrayPrefix != '') {
                $fieldName = $arrayPrefix.'['.$fieldNameO.']';
            } else {
                $fieldName = $fieldNameO;
            }
            $clientRules[$fieldName] = [];

            if (isset($field['validators'])) {
                $validators = $field['validators'];
                foreach ($validators as $validatorName => $validator) {

                    // Skip messages that are for server-side use only
                    if (isset($validator['domain']) && $validator['domain'] == 'server') {
                        continue;
                    }

                    $newRules = $this->transformValidator($fieldName, $validatorName, $validator);
                    $clientRules[$fieldName] = array_merge($clientRules[$fieldName], $newRules);
                    // Message
                    if (isset($validator['message'])) {
                        $validator = array_merge(['self' => $fieldNameOnly], $validator);
                        if (!isset($clientMessages[$fieldName])) {
                            $clientMessages[$fieldName] = [];
                        }
                        // Copy the translated message to every translated rule created by this validation rule
                        $message = $this->translator->translate($validator['message'], $validator);
                        foreach ($newRules as $translatedRuleName => $rule) {
                            $clientMessages[$fieldName][$translatedRuleName] = $message;
                        }
                    }
                }
            }
        }
        $result = [
            'rules'    => $clientRules,
            'messages' => $clientMessages,
        ];

        if ($stringEncode) {
            return json_encode($result, JSON_PRETTY_PRINT);
        } else {
            return $result;
        }
    }

    /**
     * Transform a validator for a particular field into one or more jQueryValidation rules.
     *
     * @param string   $fieldName
     * @param string   $validatorName
     * @param string[] $validator
     */
    private function transformValidator($fieldName, $validatorName, array $validator)
    {
        $transformedValidatorJson = [];
        switch ($validatorName) {
            // Required validator
            case 'email':
                $transformedValidatorJson['email'] = true;
                break;
            case 'equals':
                if (isset($validator['value'])) {
                    $transformedValidatorJson['equals'] = $validator;
                }
                break;
            case 'integer':
                $transformedValidatorJson['digits'] = true;
                break;
            case 'length':
                if (isset($validator['min']) && isset($validator['max'])) {
                    $transformedValidatorJson['rangelength'] = [
                        $validator['min'],
                        $validator['max'],
                    ];
                } elseif (isset($validator['min'])) {
                    $transformedValidatorJson['minlength'] = $validator['min'];
                } elseif (isset($validator['max'])) {
                    $transformedValidatorJson['maxlength'] = $validator['max'];
                }
                break;
            case 'matches':
                if (isset($validator['field'])) {
                    $transformedValidatorJson['matchFormField'] = $validator['field'];
                }
                break;
            case 'member_of':
                if (isset($validator['values'])) {
                    $transformedValidatorJson['memberOf'] = $validator['values'];
                }
                break;
            case 'no_leading_whitespace':
                $transformedValidatorJson['noLeadingWhitespace'] = true;
                break;
            case 'no_trailing_whitespace':
                $transformedValidatorJson['noTrailingWhitespace'] = true;
                break;
            case 'not_equals':
                if (isset($validator['value'])) {
                    $transformedValidatorJson['notEquals'] = $validator;
                }
                break;
            case 'not_matches':
                if (isset($validator['field'])) {
                    $transformedValidatorJson['notMatchFormField'] = $validator['field'];
                }
                break;
            case 'not_member_of':
                if (isset($validator['values'])) {
                    $transformedValidatorJson['notMemberOf'] = $validator['values'];
                }
                break;
            case 'numeric':
                $transformedValidatorJson['number'] = true;
                break;
            case 'range':
                if (isset($validator['min']) && isset($validator['max'])) {
                    $transformedValidatorJson['range'] = [
                        $validator['min'],
                        $validator['max'],
                    ];
                } elseif (isset($validator['min'])) {
                    $transformedValidatorJson['min'] = $validator['min'];
                } elseif (isset($validator['max'])) {
                    $transformedValidatorJson['max'] = $validator['max'];
                }
                break;
            case 'regex':
                $transformedValidatorJson['pattern'] = $validator['regex'];
                break;
            case 'required':
                $transformedValidatorJson['required'] = true;
                break;
            case 'telephone':
                $transformedValidatorJson['phoneUS'] = true;
                break;
            case 'uri':
                $transformedValidatorJson['url'] = true;
                break;
            case 'username':
                $transformedValidatorJson['username'] = true;
                break;
            default:
                break;
        }

        return $transformedValidatorJson;
    }
}
