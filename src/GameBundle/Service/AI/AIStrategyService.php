<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 3.0
 */
class AIStrategyService
{
    const STRATEGY_MAP = [
        PathProcessor::PATH_LEFT  => AIStrategyProcessor::STRATEGY_HORIZONTAL,
        PathProcessor::PATH_RIGHT => AIStrategyProcessor::STRATEGY_HORIZONTAL,
        PathProcessor::PATH_UP    => AIStrategyProcessor::STRATEGY_VERTICAL,
        PathProcessor::PATH_DOWN  => AIStrategyProcessor::STRATEGY_VERTICAL
    ];
    /**
     * @var AIStrategyProcessor
     */
    private $processor;

    public function __construct(AIStrategyProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public function chooseCells(Battlefield $battlefield) : array
    {
        foreach ($battlefield->getCells() as $cell) {
            if (!$cell->hasFlag(CellModel::FLAG_DEAD_SHIP) || CellModel::isShipDead($cell)) {
                continue;
            }

            return $this->processor->process($cell, $this->chooseStrategy($cell));
        }

        return [];
    }

    /**
     * @since 3.5
     *
     * @param Cell $cell
     *
     * @return int
     */
    private function chooseStrategy(Cell $cell) : int
    {
        $processor = new PathProcessor($cell->getCoordinate());

        $battlefield = $cell->getBattlefield();
        foreach (static::STRATEGY_MAP as $way => $strategy) {
            $processor->setPath($way);

            /** @var Cell $cell */
            if (null !== $cell = $battlefield->getCellByCoordinate($processor->getNextCoordinate())) {
                if ($cell->hasFlag(CellModel::FLAG_DEAD_SHIP)) {
                    return $strategy;
                }
            }
        }

        return AIStrategyProcessor::STRATEGY_BOTH;
    }
}
