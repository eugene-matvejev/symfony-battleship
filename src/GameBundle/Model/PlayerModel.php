<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Repository\PlayerTypeRepository;

/**
 * @since 2.0
 */
class PlayerModel
{
    const TYPE_CPU   = 1;
    const TYPE_HUMAN = 2;
    const TYPES_ALL  = [self::TYPE_CPU, self::TYPE_HUMAN];
    /**
     * @var PlayerType[]
     */
    private static $playerTypes;

    public function __construct(PlayerTypeRepository $repository)
    {
        if (null === self::$playerTypes) {
            self::$playerTypes = $repository->getAllIndexed();
        }
    }

    /**
     * @return PlayerType[]
     */
    public function getTypes() : array
    {
        return self::$playerTypes;
    }
}
