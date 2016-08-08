<?php

namespace EM\FoundationBundle\Controller;

use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
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
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_GET,
            static::$router->generate('foundation_bundle.gui.index')
        );
        $this->assertRedirectedResponse($client->getResponse());
    }

    public function registerActionProvider() : array
    {
        return [
            'not-existed before' => [
                'do.not.exists'. LoadPlayerData::TEST_PLAYER_EMAIL,
                LoadPlayerData::TEST_PLAYER_PASSWORD,
                Response::HTTP_CREATED
            ],
            'existed before'     => [
                LoadPlayerData::TEST_PLAYER_EMAIL,
                LoadPlayerData::TEST_PLAYER_PASSWORD,
                Response::HTTP_UNPROCESSABLE_ENTITY
            ]
        ];
    }

    /**
     * @group        asdasd
     * @see PlayerController::registerAction
     * @test
     *
     * @dataProvider registerActionProvider
     *
     * @param string $email
     * @param string $password
     * @param int    $expectedStatusCode
     */
    public function registerAction(string $email, string $password, int $expectedStatusCode)
    {
        $client = $this->getUnauthorizedClient();
        //$client = clone static::$client;
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('foundation_bundle.api.player_create'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            "{\"email\": \"{$email}\", \"password\": \"{$password}\"}"
        );

        $this->assertEquals($expectedStatusCode, $client->getResponse()->getStatusCode());
    }


    /**
     * @see PlayerController::processLogin
     * @test
     */
    public function processLoginOnNotExisted()
    {
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('foundation_bundle.api.player_login'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see PlayerController::processLogin
     * @test
     */
    public function processLoginOnAlreadyExistedRightCredentials()
    {
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('foundation_bundle.api.player_login'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see PlayerController::processLogin
     * @test
     */
    public function processLoginOnAlreadyExistedWrongCredentials()
    {
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_POST,
            static::$router->generate('foundation_bundle.api.player_login'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    //public function logoutAction(Request $request) : Response
    //{
    //}
    //
    ///**
    // * @see PlayerController::logoutAction
    // * @test
    // */
    //public function logoutActionOnExisted()
    //{
    //    $response = $this->requestLogoutRoute('asd');
    //
    //    $this->assertSuccessfulResponse($response);
    //}
    //
    ///**
    // * @see PlayerController::processLogin
    // * @test
    // */
    //public function logoutActionOnNonExisted()
    //{
    //    $response = $this->requestLogoutRoute('asd');
    //
    //    $this->assertUnsuccessfulResponse($response);
    //}

    private function requestLogoutRoute(string $sessionHash) : Response
    {
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_DELETE,
            static::$router->generate('foundation_bundle.api.player_logout', ['hash' => $sessionHash])
        );

        return $client->getResponse();
    }
}
