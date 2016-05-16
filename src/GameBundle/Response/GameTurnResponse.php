<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\AccessorOrder(order="custom", custom={"result","cells"})
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
        /** because JMS serializer don't work properly without array_values */
        $this->cells = array_values($cells);

        return $this;
    }

    /**
     * @return GameResult
     */
    public function getResult()
    {
        return $this->result;
    }

    public function setResult(GameResult $result) : self
    {
        $this->result = $result;

        return $this;
    }
}
