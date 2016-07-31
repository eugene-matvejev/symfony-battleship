<?php

namespace EM\FoundationBundle\Controller;

use EM\Tests\Environment\IntegrationTestSuite;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see PlayerController
 */
class PlayerControllerTest extends IntegrationTestSuite
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
            static::$router->generate('foundation_bundle.gui.index')
        );
        $this->assertRedirectedResponse($client->getResponse());
    }
}
