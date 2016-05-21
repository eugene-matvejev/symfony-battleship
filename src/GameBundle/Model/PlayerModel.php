<?php

namespace EM\GameBundle\Model;

use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Player;

/**
 * @since 2.0
 */
class PlayerModel
{
    const FLAG_NONE          = 0x00;
    const FLAG_AI_CONTROLLED = 0x01;
    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createOnRequest(string $name, int $flags = self::FLAG_NONE) : Player
    {
        $player = $this->repository->findOneBy(['name' => $name]);

        return $player ?? (new Player())
            ->setName($name)
            ->setFlags(static::createAIControlled($flags) ? static::FLAG_AI_CONTROLLED : static::FLAG_NONE);
    }

    public static function isAIControlled(Player $player) : bool
    {
        return $player->hasFlag(self::FLAG_AI_CONTROLLED);
    }

    public static function createAIControlled(int $flags) : bool
    {
        return ($flags & PlayerModel::FLAG_AI_CONTROLLED) === PlayerModel::FLAG_AI_CONTROLLED;
    }
}
