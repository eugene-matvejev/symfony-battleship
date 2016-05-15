<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\AccessorOrder(order="custom", custom={"gameResult","cells"})
 * @JMS\XmlRoot("game-turn-data")
 *
 * @since 5.0
 */
class GameTurnResponse
{
    /**
     * @JMS\Type("array<EM\GameBundle\Entity\Cell>")
     * @JMS\XmlList(entry="cell")
     * @JMS\XmlKeyValuePairs()
     *
     * @var Cell[]
     */
    private $cells = [];
    /**
     * @JMS\Type("EM\GameBundle\Entity\GameResult")
     *
     * @var GameResult
     */
    private $gameResult;

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
        /** because JMS serializer don't work properly without array_values */
        $this->cells = array_values($cells);

        return $this;
    }

    /**
     * @return GameResult
     */
    public function getGameResult()
    {
        return $this->gameResult;
    }

    public function setGameResult(GameResult $result) : self
    {
        $this->gameResult = $result;

        return $this;
    }
}
