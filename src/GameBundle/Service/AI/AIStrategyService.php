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
    private $strategyProcessor;

    public function __construct(AIStrategyProcessor $strategyProcessor)
    {
        $this->strategyProcessor = $strategyProcessor;
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

            return $this->strategyProcessor->process($cell, $this->chooseStrategy($cell));
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
        $service = new PathProcessor($cell);

        $battlefield = $cell->getBattlefield();
        foreach (self::STRATEGY_MAP as $way => $strategyId) {
            $service->setPath($way);

            $cell = $battlefield->getCellByCoordinate($service->getNextCoordinate());
            if (null !== $cell && $cell->hasFlag(CellModel::FLAG_DEAD_SHIP)) {
                return $strategyId;
            }
        }

        return AIStrategyProcessor::STRATEGY_BOTH;
    }
}
