<?php

namespace EM\GameBundle\Response;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Entity\GameResult;
use JMS\Serializer\Annotation as JMS;

/**
 * @see   GameTurnResponseTest
 *
 * @JMS\XmlRoot("game-turn-data")
 * @JMS\AccessorOrder(order="custom", custom={"result","cells"})
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
     * @param Cell[] $cells
     * @param Game   $game
     */
    public function __construct(Game $game, array $cells)
    {
        if (null !== $game->getResult()) {
            $this->setResult($game->getResult());
        }

        $this->setCells($cells);
    }

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
        $this->cells = $cells;

        return $this;
    }

    public function getResult() : GameResult
    {
        return $this->result;
    }

    public function setResult(GameResult $result) : self
    {
        $this->result = $result;

        return $this;
    }
}
