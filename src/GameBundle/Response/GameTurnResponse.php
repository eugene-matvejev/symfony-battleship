<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\GameResult;

/**
 * @since 5.0
 */
class GameTurnResponse
{
    /**
     * @var array
     */
    private $cells = [];
    /**
     * @var GameResult
     */
    private $result;

    public function addCell(Cell $cell) : self
    {
        $this->cells[] = $cell;

        return $this;
    }

    /**
     * @return Cell[]
     */
    public function getCells() : array
    {
        return $this->cells;
    }

    /**
     * @return GameResult
     */
    public function getResult()
    {
        return $this->result;
    }

    public function setGameResult(GameResult $result) : self
    {
        $this->result = $result;

        return $this;
    }
}
