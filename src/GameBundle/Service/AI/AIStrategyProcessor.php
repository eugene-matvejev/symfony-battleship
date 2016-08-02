<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @see   AIStrategyProcessorTest
 *
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

        return $this->processPaths($cell, $paths);
    }

    /**
     * @param Cell  $cell
     * @param int[] $paths
     *
     * @return Cell[]
     */
    protected function processPaths(Cell $cell, array $paths) : array
    {
        $cells = [];
        foreach ($paths as $path) {
            try {
                $cells[] = $this->processPath($cell->getBattlefield(), $path, $cell->getCoordinate());
            } catch (CellException $e) {
                continue;
            }
        }

        return $cells;
    }

    /**
     * @param Battlefield $battlefield
     * @param int         $path
     * @param string      $coordinate
     *
     * @return Cell
     * @throws CellException
     */
    protected function processPath(Battlefield $battlefield, int $path, string $coordinate) : Cell
    {
        $processor = (new PathProcessor($coordinate))->setPath($path);

        while (null !== $cell = $battlefield->getCellByCoordinate($processor->calculateNextCoordinate())) {
            if ($cell->hasFlag(CellModel::FLAG_DEAD)) {
                if ($cell->hasFlag(CellModel::FLAG_SHIP)) {
                    continue;
                }
                /** if it is not dead ship - terminate processing */
                throw new CellException("cell: {$cell->getId()} already dead and is not ship");
            }

            return $cell;
        }

        throw new CellException("unable to find cell using path: {$path} from coordinate: {$coordinate}");
    }
}
