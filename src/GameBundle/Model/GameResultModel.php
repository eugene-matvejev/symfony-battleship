<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\GameResult;

/**
 * @since 3.0
 */
class GameResultModel
{
    const TIME_FORMAT = 'd - m - Y / H:i';

    public static function getJSON(GameResult $result) : \stdClass
    {
        $std = new \stdClass();
        $std->id = $result->getId();

        $std->game = new \stdClass();
        $std->game->id = $result->getGame()->getId();

        $std->time = new \stdClass();
        $std->time->s = $result->getGame()->getTimestamp()->format(self::TIME_FORMAT);
        $std->time->f = $result->getTimestamp()->format(self::TIME_FORMAT);

        $std->player = PlayerModel::getJSON($result->getPlayer());

        return $std;
    }
}
