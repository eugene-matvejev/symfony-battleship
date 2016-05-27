<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 8.0
 */
class AIStrategyProcessor
{
    const STRATEGY_NONE       = 0x00;
    const STRATEGY_HORIZONTAL = 0x01;
    const STRATEGY_VERTICAL   = 0x02;
    const STRATEGY_BOTH       = self::STRATEGY_HORIZONTAL | self::STRATEGY_VERTICAL;

    /**
     * @param Cell $cell
     * @param int  $strategyFlag
     *
     * @return Cell[]
     */
    public function process(Cell $cell, int $strategyFlag) : array
    {
        $paths = [];
        if (($strategyFlag & static::STRATEGY_HORIZONTAL) === static::STRATEGY_HORIZONTAL) {
            $paths[] = PathProcessor::PATH_LEFT;
            $paths[] = PathProcessor::PATH_RIGHT;
        }
        if (($strategyFlag & static::STRATEGY_VERTICAL) === static::STRATEGY_VERTICAL) {
            $paths[] = PathProcessor::PATH_UP;
            $paths[] = PathProcessor::PATH_DOWN;
        }

        return $this->processCoordinates($cell, $paths);
    }

    /**
     * @param Cell  $cell
     * @param int[] $paths
     *
     * @return Cell[]
     */
    protected function processCoordinates(Cell $cell, array $paths) : array
    {
        $cells = [];
        $battlefield = $cell->getBattlefield();
        $processor = new PathProcessor($cell);

        foreach ($paths as $path) {
            $processor->setPath($path);

            /** @var Cell $cell */
            while (null !== $cell = $battlefield->getCellByCoordinate($processor->getNextCoordinate())) {
                /** if it is marked as skipped or dead water - skip processing */
                if ($cell->hasFlag(CellModel::FLAG_SKIP) || (!$cell->hasFlag(CellModel::FLAG_SHIP) && $cell->hasFlag(CellModel::FLAG_DEAD))) {
                    break;
                }
                /** if it not marked as dead return it later */
                if (!$cell->hasFlag(CellModel::FLAG_DEAD)) {
                    $cells[] = $cell;
                    break;
                }
            }
        }

        return $cells;
    }
}
