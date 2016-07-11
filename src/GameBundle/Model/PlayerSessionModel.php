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
    const TTL = 60 * 60 * 24 * 30;
    /**
     * @var EntityRepository
     */
    protected $repository;
    /**
     * @var string
     */
    protected $salt;

    public function __construct(EntityRepository $repository, PlayerModel $model, string $salt)
    {
        $this->repository = $repository;
        $this->model = $model;
        $this->salt = $salt;
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
        $player = $this->model->createOnRequestHumanControlled($email, $password);

        if (null === $player->getId() || $player->getPasswordHash() !== $this->model->generatePasswordHash($player->getEmail(), $password)) {
            throw new BadCredentialsException();
        }

        return $this->create($player);
    }

    public function create(Player $player) : PlayerSession
    {
        $session = (new PlayerSession())
            ->setPlayer($player)
            ->setHash($this->createSessionHash($player));

        return $session;
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
        if (null !== $session = $this->repository->findOneBy(['hash' => $hash])) {
            if ($session->getTimestamp()->getTimestamp() + static::TTL >= time()) {
                return $session;
            }

            throw new CredentialsExpiredException();
        }

        throw new BadCredentialsException();
    }

    protected function createSessionHash(Player $player) : string
    {
        return sha1("{$player->getEmail()}:{$player->getPasswordHash()}:{$this->salt}:" . microtime(true));
    }
}
