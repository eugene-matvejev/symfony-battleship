<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;
use EM\GameBundle\Entity\Player;

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
        $this->salt       = $salt;
    }

    public static function isAIControlled(Player $player) : bool
    {
        return $player->hasFlag(static::FLAG_AI_CONTROLLED);
    }

    public function generatePasswordHash(string $username, string $password) : string
    {
        return sha1("{$username}:{$password}:{$this->salt}");
    }

    public function createPlayer(string $email, string $password, int $flag = self::FLAG_NONE) : Player
    {
        return (new Player())
            ->setEmail($email)
            ->setPasswordHash($this->generatePasswordHash($email, $password))
            ->setFlags($flag);
    }

    public function createOnRequestAIControlled(string $email) : Player
    {
        /** @var Player $player */
        $player = $this->repository->findOneBy(['email' => $email]);

        return $player ?? $this->createPlayer($email, '', static::FLAG_AI_CONTROLLED);
    }
}
