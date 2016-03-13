<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\GameResult;

/**
 * @since 5.0
 */
class GameTurnResponse extends AbstractResponse
{
    public function __construct()
    {
        $this->data['cells'] = [];
    }

    public function addCell(Cell $cell) : self
    {
        $this->data['cells'][] = $cell;
    }

    public function setGameResult(GameResult $result) : self
    {
        $this->data['result'] = $result;
    }
}
