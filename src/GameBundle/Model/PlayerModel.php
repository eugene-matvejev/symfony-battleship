<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerType;

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

    function __construct(ObjectManager $om)
    {
        if (null === self::$playerTypes) {
            self::$playerTypes = $om->getRepository('GameBundle:PlayerType')->getTypes();
        }
    }

    public static function getJSON(Player $player) : \stdClass
    {
        return (object)[
            'id' => $player->getId(),
            'name' => $player->getName(),
            'type' => $player->getType()->getId()
        ];
    }

    /**
     * @return PlayerType[]
     */
    public function getTypes() : array
    {
        return self::$playerTypes;
    }
//
//    /**
//     * @return int[]
//     */
//    public static function getAllTypes() : array
//    {
//        return [self::TYPE_CPU, self::TYPE_HUMAN];
//    }
}