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
        return (object)[
            'id' => $result->getId(),
            'game' => (object)['id' => $result->getGame()->getId()],
            'time' => (object)[
                's' => $result->getGame()->getTimestamp()->format(self::TIME_FORMAT),
                'f' => $result->getTimestamp()->format(self::TIME_FORMAT)
            ],
            'player' => PlayerModel::getJSON($result->getPlayer())
        ];
    }
}
