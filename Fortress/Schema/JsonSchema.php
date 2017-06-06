<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/fortress
 * @license   https://github.com/userfrosting/fortress/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Fortress\Schema;

use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

/**
 * RequestSchema Class
 *
 * Represents a schema for an HTTP request, compliant with the WDVSS standard (https://github.com/alexweissman/wdvss)
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class JsonSchema extends RequestSchema
{
    /**
     * Load a schema from a JSON file.
     *
     * @param string $file Path to the schema file.
     * @throws Exception The file does not exist or is not a valid JSON schema.
     */
    public function loadSchema($path)
    {
        $doc = file_get_contents($path);
        if ($doc === false) {
            throw new FileNotFoundException("The schema '$path' could not be found.");
        }

        $schema = json_decode($doc, true);
        if ($schema === null) {
            throw new JsonException("The schema '$path' does not contain a valid JSON document.  JSON error: " . json_last_error());
        }

        $this->schema = $schema;
    }
}
