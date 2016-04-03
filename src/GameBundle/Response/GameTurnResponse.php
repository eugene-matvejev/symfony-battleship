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

    /**
     * @return Cell[]
     */
    public function getCells() : array
    {
        return $this->cells;
    }

    /**
     * @param Cell[] $cells
     *
     * @return GameTurnResponse
     */
    public function setCells(array $cells) : self
    {
        $this->cells = $cells;

        return $this;
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
