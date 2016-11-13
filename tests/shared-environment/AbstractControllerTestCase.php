<?php

namespace EM\Tests\Environment;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerSession;
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

        $player = static::$om->getRepository(Player::class)->findOneBy(['username' => $username]);
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

    /**
     * @param Player $player
     *
     * @return PlayerSession
     */
    private function mockAuthorizedSession(Player $player) : PlayerSession
    {
        $hash    = sha1(microtime(true));
        $session = new PlayerSession();
        $session->setHash($hash);
        $session->setPlayer($player);
        $player->setPasswordHash($hash);

        static::$om->persist($session);
        static::$om->persist($player);
        static::$om->flush();

        return $session;
    }
}
