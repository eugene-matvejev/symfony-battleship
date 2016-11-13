<?php

namespace EM\FoundationBundle\Controller;

use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
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
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_GET,
            '/'
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function registerActionProvider() : array
    {
        return [
            'not-existed before' => [
                Response::HTTP_CREATED,
                'do.not.exists'. LoadPlayerData::TEST_PLAYER_EMAIL,
                LoadPlayerData::TEST_PLAYER_PASSWORD
            ],
            'existed before'     => [
                Response::HTTP_UNPROCESSABLE_ENTITY,
                LoadPlayerData::TEST_PLAYER_EMAIL,
                LoadPlayerData::TEST_PLAYER_PASSWORD
            ]
        ];
    }

    /**
     * @see PlayerController::registerAction
     * @test
     *
     * @dataProvider registerActionProvider
     *
     * @param int    $expectedStatusCode
     * @param string $email
     * @param string $password
     */
    public function registerAction(int $expectedStatusCode, string $email, string $password)
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
