<?php

namespace EM\Tests\Environment\AssertionSuite;

use Symfony\Component\HttpFoundation\Response;

/**
 * @since 22.5
 */
trait ResponseAssertionSuites
{
    public function assertSuccessfulJSONResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);

        $this->assertJson($response->getContent());
    }

    public function assertSuccessfulResponse(Response $response)
    {
        $this->assertTrue($response->isSuccessful());
    }

    public function assertSuccessfulXMLResponse(Response $response)
    {
        $this->assertSuccessfulResponse($response);

        $xmlElement = simplexml_load_string($response->getContent(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xmlElement);
    }

    public function assertUnsuccessfulResponse(Response $response)
    {
        $this->assertTrue($response->isClientError() || $response->isServerError());
    }

    public function assertRedirectedResponse(Response $response)
    {
        $this->assertTrue($response->isRedirection());
    }
}
