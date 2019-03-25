<?php

/*
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\RequestSchema;

use UserFrosting\Support\Repository\Repository;

/**
 * Represents a schema for an HTTP request, compliant with the WDVSS standard (https://github.com/alexweissman/wdvss)
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class RequestSchemaRepository extends Repository implements RequestSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function setDefault($field, $value)
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        $this->items[$field]['default'] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addValidator($field, $validatorName, array $parameters = [])
    {
        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        if (!isset($this->items[$field]['validators'])) {
            $this->items[$field]['validators'] = [];
        }

        $this->items[$field]['validators'][$validatorName] = $parameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeValidator($field, $validatorName)
    {
        unset($this->items[$field]['validators'][$validatorName]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransformations($field, $transformations = [])
    {
        if (!is_array($transformations)) {
            $transformations = [$transformations];
        }

        if (!isset($this->items[$field])) {
            $this->items[$field] = [];
        }

        $this->items[$field]['transformations'] = $transformations;

        return $this;
    }
}
