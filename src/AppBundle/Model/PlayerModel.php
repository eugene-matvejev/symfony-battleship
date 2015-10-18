<?php

namespace AppBundle\Model;

class PlayerModel
{
    const TYPE_CPU   = 1;
    const TYPE_HUMAN = 2;

    /**
     * @return int[]
     */
    public static function getTypes()
    {
        return [self::TYPE_CPU, self::TYPE_HUMAN];
    }
}