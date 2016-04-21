<?php

namespace EM\GameBundle\Model;

use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Player;

/**
 * @since 2.0
 */
class PlayerModel
{
    const MASK_NONE = 0x0000;
    const MASK_AI_CONTROLLED = 0x0001;
    /**
     * @var EntityRepository
     */
    private $playerRepository;

    public function __construct(EntityRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    public function createOnRequest(string $name, bool $cpuControlled = false) : Player
    {
        $player = $this->playerRepository->findOneBy(['name' => $name]);

        return $player ?? (new Player())
            ->setName($name)
            ->setMask($cpuControlled ? self::MASK_AI_CONTROLLED : self::MASK_NONE);
    }

    public static function isAIControlled(Player $player) : bool
    {
        return $player->hasMask(self::MASK_AI_CONTROLLED);
    }
}
