<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;

/**
 * @since 3.0
 */
class AIStrategyService
{
    /**
     * @var AIStrategyProcessor
     */
    private $strategyProcessor;
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var Cell[]
     */
    private $checkedCells = [];

    public function __construct(CellModel $model, AIStrategyProcessor $strategyProcessor)
    {
        $this->cellModel = $model;
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
            if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED || $this->isShipDead($cell)) {
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
        $coordinates = [
            AIStrategyProcessor::STRATEGY_HORIZONTAL => [CoordinateService::WAY_LEFT, CoordinateService::WAY_RIGHT],
            AIStrategyProcessor::STRATEGY_VERTICAL => [CoordinateService::WAY_UP, CoordinateService::WAY_DOWN]
        ];

        $service = new CoordinateService($cell);
        foreach ($coordinates as $strategy => $ways) {
            foreach ($ways as $way) {
                $service->setWay($way)->calculateNextCoordinate();

                if (null !== $_cell = $cell->getBattlefield()->getCellByCoordinate($service->getValue())) {
                    if ($_cell->getState()->getId() === CellModel::STATE_SHIP_DIED) {
                        return $strategy;
                    }
                }
            }
        }

        return AIStrategyProcessor::STRATEGY_BOTH;
    }

    public function isShipDead(Cell $cell) : bool
    {
        if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
            return false;
        }
        if (isset($this->checkedCells[$cell->getId()])) {
            return true;
        }

        $cells = [$cell];
        $this->checkedCells[$cell->getId()] = $cell;

        $service = new CoordinateService($cell);
        foreach (CoordinateService::ALL_BASIC_WAYS as $way) {
            $service->setWay($way)->calculateNextCoordinate();

            while (null !== $_cell = $cell->getBattlefield()->getCellByCoordinate($service->getValue())) {
                if (!in_array($_cell->getState()->getId(), CellModel::STATES_SHIP)) {
                    break;
                }
                if ($_cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                    return false;
                }

                $service->calculateNextCoordinate();
                $cells[] = $cell;
                $this->checkedCells[$cell->getId()] = $cell;
            }
        }

        foreach ($cells as $cell) {
            foreach ((new CoordinateService($cell))->getAdjacentCells() as $_cell) {
                $this->cellModel->switchStateToSkipped($_cell);
            }
        }

        return true;
    }
}
