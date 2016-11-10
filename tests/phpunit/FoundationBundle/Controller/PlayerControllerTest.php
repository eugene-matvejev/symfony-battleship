<?php

namespace EM\FoundationBundle\Controller;

use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see PlayerController
 */
class PlayerControllerTest extends AbstractControllerTestCase
{
    /**
     * @see PlayerController::indexAction
     * @test
     */
    public function indexAction()
    {
        $client = static::$client;
        $client->request(
            Request::METHOD_GET,
            '/'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
