<?php

/*
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress;

use UserFrosting\Fortress\RequestSchema\RequestSchemaRepository;
use UserFrosting\Support\Repository\Loader\YamlFileLoader;

/**
 * RequestSchema Class.
 *
 * Represents a schema for an HTTP request, compliant with the WDVSS standard (https://github.com/alexweissman/wdvss)
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class RequestSchema extends RequestSchemaRepository
{
    /**
     * @var \UserFrosting\Support\Repository\Loader\FileRepositoryLoader
     */
    protected $loader;

    /**
     * Loads the request schema from a file.
     *
     * @param string $path The full path to the file containing the [WDVSS schema](https://github.com/alexweissman/wdvss).
     */
    public function __construct($path = null)
    {
        $this->items = [];

        if (!is_null($path)) {
            $this->loader = new YamlFileLoader($path);

            $this->items = $this->loader->load();
        }
    }

    /**
     * @deprecated since 4.1. Use `all()` instead
     *
     * @return array The schema data.
     */
    public function getSchema()
    {
        return $this->items;
    }
}
