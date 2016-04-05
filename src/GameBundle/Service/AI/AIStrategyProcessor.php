<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;

/**
 * @since 8.0
 */
class AIStrategyProcessor
{
    const STRATEGY_HORIZONTAL = 0;
    const STRATEGY_VERTICAL   = 1;
    const STRATEGY_BOTH       = 2;

    /**
     * @param Cell $cell
     * @param int  $strategyId
     *
     * @return Cell[]
     */
    public function process(Cell $cell, int $strategyId) : array
    {
        $service = new CoordinateService($cell);

        switch ($strategyId) {
            case self::STRATEGY_HORIZONTAL:
                return $this->processCoordinates($cell->getBattlefield(), [
                    clone $service->setWay(CoordinateService::WAY_LEFT),
                    clone $service->setWay(CoordinateService::WAY_RIGHT)
                ]);
            case self::STRATEGY_VERTICAL:
                return $this->processCoordinates($cell->getBattlefield(), [
                    clone $service->setWay(CoordinateService::WAY_UP),
                    clone $service->setWay(CoordinateService::WAY_DOWN)
                ]);
            case self::STRATEGY_BOTH:
                return $this->processCoordinates($cell->getBattlefield(), [
                    clone $service->setWay(CoordinateService::WAY_LEFT),
                    clone $service->setWay(CoordinateService::WAY_RIGHT),
                    clone $service->setWay(CoordinateService::WAY_UP),
                    clone $service->setWay(CoordinateService::WAY_DOWN)
                ]);
        }
    }

    /**
     * @param Battlefield         $battlefield
     * @param CoordinateService[] $coordinates
     *
     * @return Cell[]
     */
    protected function processCoordinates(Battlefield $battlefield, array $coordinates) : array
    {
        $cells = [];
        foreach ($coordinates as $coordinate) {
            while (null !== $cell = $battlefield->getCellByCoordinate($coordinate->getNextCoordinate())) {
                if (in_array($cell->getState()->getId(), CellModel::STATES_SKIP_STRATEGY_PROCESSING)) {
                    break;
                } elseif (in_array($cell->getState()->getId(), CellModel::STATES_LIVE)) {
                    $cells[] = $cell;
                    break;
                }
            }
        }

        return $cells;
    }
}
