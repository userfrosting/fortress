<?php

/*
 * UserFrosting Fortress (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @copyright Copyright (c) 2013-2019 Alexander Weissman
 * @license   https://github.com/userfrosting/fortress/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Fortress\Tests;

use UserFrosting\I18n\DictionaryInterface;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\Support\Repository\Repository;

class DictionaryStub extends Repository implements DictionaryInterface
{
    public function __construct()
    {
    }

    public function getDictionary(): array
    {
        return [];
    }

    public function getLocale(): LocaleInterface
    {
    }

    public function getFlattenDictionary(): array
    {
    }
}