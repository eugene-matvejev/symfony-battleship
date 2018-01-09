<?php

namespace EM\Tests\Behat;

use JsonSchema\Validator;

class JSONSchemaValidationContext extends CommonControllerContext
{
    protected function getJSONContent(string $filename) : string
    {
        return $this->getSharedFixtureContent("../../vendor/eugene-matvejev/battleship-game-api-json-schema/${filename}");
    }

    /**
     * @Given submit data from :jsonFile to API :route via :method
     *
     * @param string $jsonFile
     * @param string $route
     * @param string $method
     */
    public function submitDataFromApiGameResultsPageRouteVia(string $jsonFile, string $route, string $method)
    {
        $json = static::getJSONContent("stubs/valid/{$jsonFile}");
        $this->requestAPIRoute($route, $method, $json);
    }

    /**
     * @Then validate response against schema :schemaFile
     *
     * @param string $schemaFile
     */
    public function validateResponseAgainstSchema(string $schemaFile)
    {
        $schema = $this->getJSONContent("schema/{$schemaFile}");

        $validator = new Validator();
        $json = static::$client->getResponse()->getContent();

        $schemaObj = json_decode($schema);
        $jsonObj = json_decode($json);
        $validator->validate($jsonObj, $schemaObj);
        static::assertTrue($validator->isValid());
    }
}
