<?php

namespace AppBundle\Model;

use AppBundle\Entity\Player;

class PlayerModel
{
    const TYPE_CPU   = 1;
    const TYPE_HUMAN = 2;

    /**
     * @return int[]
     */
    public static function getAllTypes()
    {
        return [self::TYPE_CPU, self::TYPE_HUMAN];
    }

    /**
     * @param Player $player
     *
     * @return \stdClass
     */
    public static function getJSON(Player $player)
    {
        $std = new \stdClass();
        $std->id = $player->getId();
        $std->type = $player->getType()->getId();
        $std->name = $player->getName();

        return $std;
    }
}