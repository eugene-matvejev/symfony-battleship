<?php

namespace EM\GameBundle\Model;

use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerSession;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

/**
 * @since 22.0
 */
class PlayerSessionModel
{
    /**
     * @var EntityRepository
     */
    protected $repository;
    /**
     * @var string
     */
    protected $salt;
    const TTL = 60 * 60 * 24 * 30;

    public function __construct(EntityRepository $repository, string $salt)
    {
        $this->repository = $repository;
        $this->salt = $salt;
    }

    public function find(string $hash) : PlayerSession
    {
        /** @var PlayerSession $session */
        if (null !== $session = $this->repository->findOneBy(['hash' => $hash])) {
            if (!$session->getTimestamp()->getTimestamp() + static::TTL >= time()) {
                throw new CredentialsExpiredException();
            }

            return $session;
        }

        throw new BadCredentialsException();
    }

    public function create(Player $player) : PlayerSession
    {
        $session = (new PlayerSession())
            ->setPlayer($player)
            ->setHash($this->createSessionHash($player));

        return $session;
    }

    protected function createSessionHash(Player $player) : string
    {
        return sha1("{$player->getEmail()}:{$player->getPassword()}:{$this->salt}:" . microtime(true));
    }
}
