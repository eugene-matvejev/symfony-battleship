<?php

namespace EM\GameBundle\Model;

use Doctrine\ORM\EntityRepository;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Repository\PlayerTypeRepository;

/**
 * @since 2.0
 */
class PlayerModel
{
    const TYPE_CPU = 1;
    const TYPE_HUMAN = 2;
    const TYPES_ALL = [self::TYPE_CPU, self::TYPE_HUMAN];
    /**
     * @var PlayerType[]
     */
    private static $cachedTypes;
    /**
     * @var EntityRepository
     */
    private $playerRepository;

    public function __construct(EntityRepository $playerRepository, PlayerTypeRepository $playerTypeRepository)
    {
        $this->playerRepository = $playerRepository;
        if (null === self::$cachedTypes) {
            self::$cachedTypes = $playerTypeRepository->getAllIndexed();
        }
    }

    /**
     * @return PlayerType[]
     */
    public function getTypes() : array
    {
        return self::$cachedTypes;
    }

    public function createOnRequest(string $name, int $typeId = self::TYPE_HUMAN) : Player
    {
        $player = $this->playerRepository->findOneBy(['name' => $name]);

        return $player ?? (new Player())
            ->setName($name)
            ->setType(self::$cachedTypes[$typeId]);
    }

    public function isCPU(Player $player) : bool
    {
        return $player->getType()->getId() === self::TYPE_CPU;
    }
}
