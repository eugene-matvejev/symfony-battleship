<?php

namespace GameBundle\Model;

use GameBundle\Entity\Player;

/**
 * @since 2.0
 */
class PlayerModel
{
    const TYPE_CPU   = 1;
    const TYPE_HUMAN = 2;

    /**
     * @return int[]
     */
    public static function getAllTypes() : array
    {
        return [self::TYPE_CPU, self::TYPE_HUMAN];
    }

    /**
     * @param Player $player
     *
     * @return \stdClass
     */
    public static function getJSON(Player $player) : \stdClass
    {
        $std = new \stdClass();
        $std->id = $player->getId();
        $std->type = $player->getType()->getId();
        $std->name = $player->getName();

        return $std;
    }
}