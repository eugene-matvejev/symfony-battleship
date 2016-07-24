<?php

namespace EM\FoundationBundle\Controller;

use EM\Tests\Environment\IntegrationTestSuite;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_GET,
            static::$router->generate('battleship_game.gui.index')
        );
        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::registerAction
     * @test
     */
    public function registerActionOnNotExistedBefore()
    {
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship_game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::registerAction
     * @test
     */
    public function registerActionOnAlreadyExistedBefore()
    {
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship_game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::processLogin
     * @test
     */
    public function processLoginOnNotExisted()
    {
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship_game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::processLogin
     * @test
     */
    public function processLoginOnAlreadyExistedRightCredentials()
    {
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship_game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see GameController::processLogin
     * @test
     */
    public function processLoginOnAlreadyExistedWrongCredentials()
    {
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('battleship_game.api.init'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    public function logoutAction(Request $request) : Response
    {
    }

    /**
     * @see GameController::logoutAction
     * @test
     */
    public function logoutActionOnExisted()
    {
        $response = $this->requestLogoutRoute('asd');

        $this->assertSuccessfulResponse($response);
    }

    /**
     * @see GameController::processLogin
     * @test
     */
    public function logoutActionOnNonExisted()
    {
        $response = $this->requestLogoutRoute('asd');

        $this->assertUnsuccessfulResponse($response);
    }

    private function requestLogoutRoute(string $sessionHash) : Response
    {
        $client = $this->getNonAuthorizedClient();
        $client->request(
            Request::METHOD_DELETE,
            static::$router->generate('foundation_bundle.api.player_logout', ['hash' => $sessionHash])
        );

        return $client->getResponse();
    }
}

