<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

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
        $pathProcessor = new PathProcessor($cell);
        $pathProcessors = [];
        switch ($strategyId) {
            case self::STRATEGY_HORIZONTAL:
                $pathProcessors = [
                    clone $pathProcessor->setPath(PathProcessor::PATH_LEFT),
                    clone $pathProcessor->setPath(PathProcessor::PATH_RIGHT)
                ];
                break;
            case self::STRATEGY_VERTICAL:
                $pathProcessors = [
                    clone $pathProcessor->setPath(PathProcessor::PATH_UP),
                    clone $pathProcessor->setPath(PathProcessor::PATH_DOWN)
                ];
                break;
            case self::STRATEGY_BOTH:
                $pathProcessors = [
                    clone $pathProcessor->setPath(PathProcessor::PATH_LEFT),
                    clone $pathProcessor->setPath(PathProcessor::PATH_RIGHT),
                    clone $pathProcessor->setPath(PathProcessor::PATH_UP),
                    clone $pathProcessor->setPath(PathProcessor::PATH_DOWN)
                ];
                break;
        }

        return $this->processCoordinates($cell->getBattlefield(), $pathProcessors);
    }

    /**
     * @param Battlefield     $battlefield
     * @param PathProcessor[] $coordinates
     *
     * @return Cell[]
     */
    protected function processCoordinates(Battlefield $battlefield, array $coordinates) : array
    {
        $cells = [];
        foreach ($coordinates as $coordinate) {
            while (null !== $cell = $battlefield->getCellByCoordinate($coordinate->getNextCoordinate())) {
                if ($cell->hasFlag(CellModel::FLAG_SKIP)
                    || (!$cell->hasFlag(CellModel::FLAG_SHIP) && $cell->hasFlag(CellModel::FLAG_DEAD))
                ) {
                    break;
                }
                if (!$cell->hasFlag(CellModel::FLAG_DEAD)) {
                    $cells[] = $cell;
                    break;
                }
            }
        }

        return $cells;
    }
}
