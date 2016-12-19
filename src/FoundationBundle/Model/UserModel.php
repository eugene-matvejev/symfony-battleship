<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;
use EM\FoundationBundle\Entity\User;

/**
 * @since 23.0
 */
class UserModel
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

    public static function isAIControlled(User $player) : bool
    {
        return $player->hasFlag(static::FLAG_AI_CONTROLLED);
    }

    public function generatePasswordHash(string $username, string $password) : string
    {
        return sha1("{$username}:{$password}:{$this->salt}");
    }

    public function createPlayer(string $email, string $password, int $flag = self::FLAG_NONE) : User
    {
        return (new User())
            ->setEmail($email)
            ->setPasswordHash($this->generatePasswordHash($email, $password))
            ->setFlags($flag);
    }

    public function createOnRequestAIControlled(string $email) : User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['email' => $email]);

        return $user ?? $this->createPlayer($email, '', static::FLAG_AI_CONTROLLED);
    }
}
