<?php

namespace EM\FoundationBundle\Controller;

use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Request;

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
        $client = clone static::$client;
        $client->request(
            Request::METHOD_GET,
            '/'
        );
        $this->assertRedirectedResponse($client->getResponse());
    }
}
