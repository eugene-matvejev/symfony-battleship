<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerSession;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

/**
 * @since 23.0
 */
class PlayerSessionModel
{
    const TTL            = 60 * 60 * 24 * 30;
    const SESSION_HEADER = 'x-wsse';
    /**
     * @var ObjectRepository
     */
    private $sessionRepository;
    /**
     * @var ObjectRepository
     */
    private $playerRepository;
    /**
     * @var PlayerModel
     */
    private $model;
    /**
     * @var string
     */
    private $salt;

    public function __construct(ObjectRepository $sessionRepository, ObjectRepository $playerRepository, PlayerModel $model, string $salt)
    {
        $this->sessionRepository = $sessionRepository;
        $this->playerRepository  = $playerRepository;
        $this->model             = $model;
        $this->salt              = $salt;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return PlayerSession
     * @throws BadCredentialsException
     */
    public function authenticate(string $email, string $password) : PlayerSession
    {
        $passwordHash = $this->model->generatePasswordHash($email, $password);

        /** @var Player $player */
        $player = $this->playerRepository->findOneBy(['email' => $email, 'passwordHash' => $passwordHash]);

        if (!$player) {
            throw new BadCredentialsException();
        }

        return $this->create($player);
    }

    /**
     * @param string $hash
     *
     * @return PlayerSession
     * @throws CredentialsExpiredException
     * @throws BadCredentialsException
     */
    public function find(string $hash) : PlayerSession
    {
        /** @var PlayerSession $session */
        if (null !== $session = $this->sessionRepository->findOneBy(['hash' => $hash])) {
            if ($session->getTimestamp()->getTimestamp() + static::TTL >= time()) {
                return $session;
            }

            throw new CredentialsExpiredException();
        }

        throw new BadCredentialsException();
    }

    private function create(Player $player) : PlayerSession
    {
        $session = (new PlayerSession())
            ->setPlayer($player)
            ->setHash($this->generateSessionHash($player));

        return $session;
    }

    private function generateSessionHash(Player $player) : string
    {
        return sha1("{$player->getEmail()}:{$player->getPasswordHash()}:{$this->salt}:" . microtime(true));
    }
}
