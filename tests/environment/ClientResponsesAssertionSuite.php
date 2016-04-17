<?php

namespace EM\Tests\Environment;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since 11.3
 */
abstract class ClientResponsesAssertionSuite extends WebTestCase
{
    public function assertSuccessfulResponse(Response $response)
    {
        $this->assertGreaterThanOrEqual(Response::HTTP_OK, $response->getStatusCode());
        $this->assertLessThan(Response::HTTP_MULTIPLE_CHOICES, $response->getStatusCode());
    }

    public function assertSuccessfulJSONResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);

        $this->assertJson($response->getContent());
    }

    public function assertSuccessfulXMLResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);

        $xmlElement = simplexml_load_string($response->getContent());
        $this->assertInstanceOf(\SimpleXMLElement::class, $xmlElement);
    }

    public function assertUnsuccessfulResponse(Response $response)
    {
        $this->assertGreaterThanOrEqual(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertLessThanOrEqual(Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED, $response->getStatusCode());
    }
}
