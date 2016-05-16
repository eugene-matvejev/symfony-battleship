<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("game-turn-data")
 * @Serializer\AccessorOrder(order="custom", custom={"result","cells"})
 *
 * @since 5.0
 */
class GameTurnResponse
{
    /**
     * @Serializer\Type("array<EM\GameBundle\Entity\Cell>")
     * @Serializer\XmlList(entry="cell")
     * @Serializer\XmlKeyValuePairs()
     *
     * @var Cell[]
     */
    private $cells = [];
    /**
     * @Serializer\Type("EM\GameBundle\Entity\GameResult")
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
