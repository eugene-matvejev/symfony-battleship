<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Exception\PlayerException;

/**
 * @since 2.0
 */
class PlayerModel
{
    const FLAG_NONE          = 0x00;
    const FLAG_AI_CONTROLLED = 0x01;
    /**
     * @var ObjectRepository
     */
    private $repository;
    /**
     * @var string
     */
    private $salt;

    public function __construct(ObjectRepository $repository, string $salt)
    {
        $this->repository = $repository;
        $this->salt = $salt;
    }

    public function generatePasswordHash(string $username, string $password) : string
    {
        return sha1("{$username}:{$password}:{$this->salt}");
    }

    /**
     * @param string $email
     *
     * @return Player
     * @throws PlayerException
     */
    public function createOnRequestAIControlled(string $email) : Player
    {
        return $this->createOnRequest($email, '', true);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return Player
     * @throws PlayerException
     */
    public function register(string $email, string $password) : Player
    {
        /** @var Player $player */
        if (null !== $player = $this->repository->findOneBy(['email' => $email])) {
            throw new PlayerException("player with '$email' already exists");
        }

        return (new Player())
            ->setEmail($email)
            ->setPasswordHash($this->generatePasswordHash($email, $password))
            ->setFlags(static::FLAG_NONE);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return Player
     * @throws PlayerException
     */
    public function createOnRequestHumanControlled(string $email, string $password) : Player
    {
        return $this->createOnRequest($email, $password, false);
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool   $controlledByAI
     *
     * @return Player
     * @throws PlayerException
     */
    protected function createOnRequest(string $email, string $password, bool $controlledByAI) : Player
    {
        /** @var Player $player */
        $player = $this->repository->findOneBy(['email' => $email]);

        if (null !== $player && $controlledByAI !== static::isAIControlled($player)) {
            throw new PlayerException("player with '{$email}' already exists and 'controlledByAI' do not match");
        }

        return $player ?? (new Player())
            ->setEmail($email)
            ->setPasswordHash($this->generatePasswordHash($email, $password))
            ->setFlags($controlledByAI ? static::FLAG_AI_CONTROLLED : static::FLAG_NONE);
    }

    public static function isAIControlled(Player $player) : bool
    {
        return $player->hasFlag(self::FLAG_AI_CONTROLLED);
    }
}
