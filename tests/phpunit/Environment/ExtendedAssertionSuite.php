<?php

namespace EM\Tests\PHPUnit\Environment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 7.2
 */
abstract class ExtendedAssertionSuite extends WebTestCase
{
    public function assertSuccessfulResponse(Response $response)
    {
        $this->assertTrue(false);
        $this->assertGreaterThanOrEqual(Response::HTTP_OK, $response->getStatusCode());
        $this->assertLessThan(Response::HTTP_MULTIPLE_CHOICES, $response->getStatusCode());
    }

    public function assertSuccessfulJSONResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);
        $this->assertJson($response->getContent());
    }

    public function assertUnsuccessfulResponse(Response $response)
    {
        $this->assertGreaterThanOrEqual(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertLessThanOrEqual(Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED, $response->getStatusCode());
    }
}
