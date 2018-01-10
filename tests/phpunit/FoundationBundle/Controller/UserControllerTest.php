<?php

namespace EM\FoundationBundle\Controller;

use EM\FoundationBundle\DataFixtures\ORM\UsersFixture;
use EM\FoundationBundle\Entity\UserSession;
use EM\Tests\Environment\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see UserController
 */
class UserControllerTest extends AbstractControllerTestCase
{
    public function registerActionProvider() : array
    {
        $freshEmail    = sha1(microtime(true)) . UsersFixture::TEST_PLAYER_EMAIL;
        $existingEmail = UsersFixture::TEST_PLAYER_EMAIL;
        $password      = UsersFixture::TEST_PLAYER_PASSWORD;

        return [
            [Response::HTTP_CREATED, "{\"email\": \"{$freshEmail}\", \"password\": \"{$password}\"}"],
            [Response::HTTP_BAD_REQUEST, '{"email": "example@example.com"}'],
            [Response::HTTP_BAD_REQUEST, '{"password": "password"}'],
            [Response::HTTP_BAD_REQUEST, '{}'],
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
        $email    = UsersFixture::TEST_PLAYER_EMAIL;
        $password = UsersFixture::TEST_PLAYER_PASSWORD;

        return [
            [Response::HTTP_CREATED, "{\"email\": \"{$email}\", \"password\": \"{$password}\"}"],
            [Response::HTTP_BAD_REQUEST, '{"email": "example@example.com"}'],
            [Response::HTTP_UNAUTHORIZED, '{"email": "not-exists@example.com", "password": "password"}']
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
        $client = $this->getAuthorizedClient(UsersFixture::TEST_PLAYER_EMAIL);
        $client->request(
            Request::METHOD_DELETE,
            '/api/player/logout'
        );

        $sessionHash = $client->getServerParameter(static::AUTH_HEADER);
        $response    = $client->getResponse();

        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());

        $session = static::$om->getRepository(UserSession::class)->findOneBy(['hash' => $sessionHash]);
        static::assertNull($session);
    }
}
