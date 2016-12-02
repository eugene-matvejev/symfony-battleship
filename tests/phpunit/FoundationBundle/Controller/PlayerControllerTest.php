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
    public function registerActionProvider() : array
    {
        $freshEmail    = sha1(microtime(true)) . LoadPlayerData::TEST_PLAYER_EMAIL;
        $existingEmail = LoadPlayerData::TEST_PLAYER_EMAIL;
        $password      = LoadPlayerData::TEST_PLAYER_PASSWORD;

        return [
            [Response::HTTP_CREATED, "{\"email\": \"{$freshEmail}\", \"password\": \"{$password}\"}"],
            [Response::HTTP_UNPROCESSABLE_ENTITY, "{\"email\": \"{$existingEmail}\", \"password\": \"{$password}\"}"]
        ];
    }

    /**
     * @see          PlayerController::registerAction
     * @test
     *
     * @dataProvider registerActionProvider
     *
     * @param int    $expectedStatusCode
     * @param string $json
     */
    public function registerAction(int $expectedStatusCode, string $json)
    {
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_POST,
            '/api/player/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            $json
        );

        $response = $client->getResponse();

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
    }

    /**
     *
     */
    public function loginActionDataProvider() : array
    {
        $email    = LoadPlayerData::TEST_PLAYER_EMAIL;
        $password = LoadPlayerData::TEST_PLAYER_PASSWORD;

        return [
            [Response::HTTP_CREATED, "{\"email\": \"{$email}\", \"password\": \"{$password}\"}"],
            [Response::HTTP_BAD_REQUEST, "{\"email\": \"{$email}\"}"]
        ];
    }

    /**
     * @see          PlayerController::loginAction
     * @test
     *
     * @dataProvider loginActionDataProvider
     *
     * @param int    $expectedStatusCode
     * @param string $json
     */
    public function loginAction(int $expectedStatusCode, string $json)
    {
        $client = $this->getUnauthorizedClient();
        $client->request(
            Request::METHOD_POST,
            '/api/player/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json'],
            $json
        );

        $response = $client->getResponse();

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
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
