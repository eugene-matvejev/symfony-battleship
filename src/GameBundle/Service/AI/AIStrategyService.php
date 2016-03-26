<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\{
    Battlefield, Cell
};
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\Strategy\{
    RandomStrategy, XStrategy, YStrategy
};
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;

/**
 * @since 3.0
 */
class AIStrategyService
{
    const STRATEGY_X = 0;
    const STRATEGY_Y = 1;
    const STRATEGY_RAND = 2;
    /**
     * @var XStrategy
     */
    private $xStrategy;
    /**
     * @var YStrategy
     */
    private $yStrategy;
    /**
     * @var RandomStrategy
     */
    private $randStrategy;
    /**
     * @var CellModel
     */
    private $cellModel;

    public function __construct(CellModel $model, XStrategy $xStrategy, YStrategy $yStrategy, RandomStrategy $randomStrategy)
    {
        $this->cellModel = $model;
        $this->xStrategy = $xStrategy;
        $this->yStrategy = $yStrategy;
        $this->randStrategy = $randomStrategy;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public function attack(Battlefield $battlefield) : array
    {
        foreach ($battlefield->getCells() as $cell) {
            if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED || $this->isShipDead($cell)) {
                continue;
            }

            switch ($this->decideStrategy($cell)) {
                case self::STRATEGY_X:
                    return $this->xStrategy->verify($cell);
                case self::STRATEGY_Y:
                    return $this->yStrategy->verify($cell);
                default:
                case self::STRATEGY_RAND:
                    return $this->randStrategy->verify($cell);
            }
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
    private function decideStrategy(Cell $cell) : int
    {
        $coordinates = [
            self::STRATEGY_X => CoordinateService::STRATEGY_X,
            self::STRATEGY_Y => CoordinateService::STRATEGY_Y
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

        return self::STRATEGY_RAND;
    }

    public function isShipDead(Cell $cell) : bool
    {
        if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
            return false;
        }

        $cells = [$cell];

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
