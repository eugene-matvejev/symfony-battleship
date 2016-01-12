<?php

namespace GameBundle\Model;

use GameBundle\Entity\GameResult;

/**
 * @since 3.0
 */
class GameResultModel
{
    /**
     * @param GameResult $result
     *
     * @return \stdClass
     */
    public function getJSON(GameResult $result) : \stdClass
    {
        $std = new \stdClass();
        $std->winner = $result->getPlayer()->getId();
        $std->game = $result->getGame()->getId();
        $std->time = $result->getTimestamp();

        return $std;
    }
}
