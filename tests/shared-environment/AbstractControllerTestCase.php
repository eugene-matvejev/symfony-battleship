<?php

namespace EM\Tests\Environment;

use EM\FoundationBundle\Entity\{User, UserSession};
use EM\Tests\Environment\AssertionSuite\ResponseAssertionSuites;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * @since 22.7
 */
abstract class AbstractControllerTestCase extends AbstractKernelTestSuite
{
    use ResponseAssertionSuites;
    /**
     * @var string
     */
    const AUTH_HEADER = 'HTTP_x-wsse';

    protected function getAuthorizedClient(string $username) : Client
    {
        $username = trim($username);
        if (empty($username)) {
            return $this->getUnauthorizedClient();
        }

        $player = static::$om->getRepository(User::class)->findOneBy(['email' => $username]);
        if (null === $player) {
            throw new \Exception("user with username: {$username} not found");
        }

        $session = $this->mockAuthorizedSession($player);

        return $this->createClientWithAuthHeader($session->getHash());
    }

    protected function getUnauthorizedClient() : Client
    {
        return $this->createClientWithAuthHeader('');
    }

    private function createClientWithAuthHeader(string $token) : Client
    {
        $client = static::$container->get('test.client');
        $client->restart();

        $client->setServerParameters([
            /** API auth */
            static::AUTH_HEADER => $token
        ]);

        return $client;
    }

    private function mockAuthorizedSession(User $user) : UserSession
    {
        $hash    = sha1(microtime(true));
        $session = new UserSession();
        $session->setHash($hash);
        $session->setUser($user);
        $user->setPasswordHash($hash);

        static::$om->persist($session);
        static::$om->persist($user);
        static::$om->flush();

        return $session;
    }
}
