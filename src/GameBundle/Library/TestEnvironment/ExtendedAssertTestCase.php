<?php

namespace GameBundle\Library\TestEnvironment;

use Symfony\Component\HttpFoundation\Response;

/**
 * @since 1.0
 */
class ExtendedAssertTestCase extends ExtendedTestCase
{
    /**
     * @param Response $response
     *
     * @return void
     */
    public function assertCorrectResponse(Response $response)
    {
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @param Response $response
     *
     * @return mixed
     */
    public function assertJsonCorrectResponse(Response $response)
    {
        $this->assertCorrectResponse($response);

        $json = json_decode($response->getContent(), true);

        return $json;
    }
}