<?php

namespace EM\FoundationBundle\Controller;

use EM\GameBundle\DataFixtures\ORM\LoadPlayerData;
use EM\GameBundle\Entity\PlayerSession;
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
                sha1(microtime(true)) . LoadPlayerData::TEST_PLAYER_EMAIL,
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
     * @see          PlayerController::registerAction
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
        $client->request(
            Request::METHOD_POST,
            '/api/player/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            "{\"email\": \"{$email}\", \"password\": \"{$password}\"}"
        );

        $response = $client->getResponse();

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
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
            '/api/player/login',
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
            '/api/player/login',
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
            '/api/player/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            '{"email": "test@test.test", "password": "password"}'
        );

        $this->assertSuccessfulResponse($client->getResponse());
    }

    /**
     * @see PlayerController::logoutAction
     * @test
     */
    public function logoutAction()
    {
        $client = $this->getAuthorizedClient(LoadPlayerData::TEST_PLAYER_EMAIL);
        $client->request(
            Request::METHOD_DELETE,
            "/api/player/logout"
        );

        $sessionHash = $client->getServerParameter(static::AUTH_HEADER);
        $response    = $client->getResponse();

        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());

        $session = static::$om->getRepository(PlayerSession::class)->findOneBy(['hash' => $sessionHash]);
        static::assertNull($session);
    }
}
