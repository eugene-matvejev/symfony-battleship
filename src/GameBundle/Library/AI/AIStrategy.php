<?php

namespace GameBundle\Library\AI;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Model\BattlefieldModel;
use GameBundle\Model\CellModel;
use Symfony\Bridge\Monolog\Logger;

/**
 * @since 3.0
 */
class AIStrategy
{
    /**
     * @var int
     */
    private $minShipSize;
    /**
     * @var int
     */
    private $maxShipSize;
    /**
     * @var CellModel
     */
    private $cellModel;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param int       $min
     * @param int       $max
     * @param CellModel $model
     * @param Logger    $logger
     */
    public function __construct(int $min, int $max, CellModel $model, Logger $logger)
    {
        $this->minShipSize = $min;
        $this->maxShipSize = $max;
        $this->cellModel = $model;
        $this->logger = $logger;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public function chooseStrategy(Battlefield $battlefield) : array
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED || $this->isShipDead($cell)) {
                continue;
            }

            $this->logger->addDebug('X STRATEGY');
            if($cells = $this->xStrategy($cell)) {
                $this->logger->addDebug('>>> X');
            }

            $this->logger->addDebug('Y STRATEGY');
            if(empty($cells) && $cells = $this->yStrategy($cell)) {
                $this->logger->addDebug('>>> Y');
            }

            $this->logger->addDebug('Z STRATEGY');
            if(empty($cells) && $cells = $this->zStrategy($cell)) {
                $this->logger->addDebug('>>> Z');
            }

            if(!empty($cells)) {
                return $cells;
            }
        }

        return [];
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function xStrategy(Cell $cell) : array
    {
        $coordinates = [
            ['x' => $cell->getX() - 1, 'y' => $cell->getY()],
            ['x' => $cell->getX() + 1, 'y' => $cell->getY()]
        ];

        return $this->strategy($cell->getBattlefield(), $coordinates, 'x');
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function yStrategy(Cell $cell) : array
    {
        $coordinates = [
            ['x' => $cell->getX(), 'y' => $cell->getY() - 1],
            ['x' => $cell->getX(), 'y' => $cell->getY() + 1]
        ];

        return $this->strategy($cell->getBattlefield(), $coordinates, 'y');
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function zStrategy(Cell $cell) : array
    {
        $cells = [];
        $coordinates = [
            ['x' => $cell->getX() + 1, 'y' => $cell->getY()],
            ['x' => $cell->getX() - 1, 'y' => $cell->getY()],
            ['x' => $cell->getX(), 'y' => $cell->getY() + 1],
            ['x' => $cell->getX(), 'y' => $cell->getY() - 1],
        ];

        foreach($coordinates as $coordinate) {
            if(null !== $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinate['x'], $coordinate['y'])) {
                $this->logger->addDebug(__FUNCTION__ .': cell: '. $cell->getId() .' state: '. $cell->getState()->getId());
                $this->logger->addDebug(__FUNCTION__ .': x'. $cell->getX() .' y:'. $cell->getY());
                if(in_array($_cell->getState()->getId(), CellModel::getLiveStates()))
                    $cells[] = $_cell;
            }
        }

        return $cells;
    }

    /**
     * @param Battlefield $battlefield
     * @param array       $coordinates
     * @param string      $axis
     *
     * @return Cell[]
     */
    private function strategy(Battlefield $battlefield, array $coordinates, string $axis) : array
    {
        $cells = [];

        for($i = 0; $i < $this->maxShipSize; $i++) {
            foreach($coordinates as $i => $coordinate) {
                if(null !== $cell = BattlefieldModel::getCellByCoordinates($battlefield, $coordinate['x'], $coordinate['y'])) {
                    $this->logger->addCritical(':::: :::: '. __FUNCTION__ .' x: '. $cell->getX() .' y: '. $cell->getY());
                    if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
                        $cells[$i] = $cell;
                        unset($coordinates[$i]);
                    } elseif(0 === $i) {
                        $coordinates[$i][$axis]++;
                    } elseif(1 === $i) {
                        $coordinates[$i][$axis]--;
                    }
                } else {
                    unset($coordinates[$i]);
                }
            }
        }

        return $cells;
    }

    private function isShipDead(Cell $cell) : bool
    {
        $xCoordinates = [
            ['x' => $cell->getX() - 1, 'y' => $cell->getY()],
            ['x' => $cell->getX() + 1, 'y' => $cell->getY()]
        ];
        $yCoordinates = [
            ['x' => $cell->getX(), 'y' => $cell->getY() - 1],
            ['x' => $cell->getX(), 'y' => $cell->getY() + 1]
        ];

        return $this->verifyShipByAxis($cell, $xCoordinates, 'x') || $this->verifyShipByAxis($cell, $yCoordinates, 'y');
    }

    /**
     * @param Cell    $cell
     * @param int[][] $coordinates
     * @param string  $axis
     *
     * @return bool
     */
    private function verifyShipByAxis(Cell $cell, array $coordinates, string $axis) : bool
    {
        $cells = [];
        $leftCell = $rightCell = true;

        $matches = 1;

        for($i = 0; $i < $this->maxShipSize; $i++) {
            if(true === $leftCell && true === $leftCell = $this->verifyWay($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y'])) {
                $cells[] = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y']);
                $matches++;
            }
            if(true === $rightCell && true === $rightCell = $this->verifyWay($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y'])) {
                $cells[] = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y']);
                $matches++;
            }

            $coordinates[0][$axis]--;
            $coordinates[1][$axis]++;
        }

        if(true === $leftCell && true === $rightCell || $matches >= $this->maxShipSize) {
            $this->logger->addCritical(__FUNCTION__ .': '. $axis .'1true');
            /**  @var Cell $_cell */
            foreach($cells as $_cell) {
                $coordinates = [
                    [
                        'x' => $_cell->getX(),
                        'y' => $_cell->getY()
                    ],
                    [
                        'x' => $_cell->getX(),
                        'y' => $_cell->getY()
                    ]
                ];

                $coordinates[0][$axis]--;
                $coordinates[1][$axis]++;

                $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y']);
                if(null !== $cell) {
                    $this->cellModel->markAsSkipped($_cell);
                }
                $cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y']);
                if(null !== $cell) {
                    $this->cellModel->markAsSkipped($_cell);
                }
            }
            return true;
        }

        return false;
    }

    /**
     * @param Battlefield $battlefield
     * @param int         $x
     * @param int         $y
     *
     * @return bool
     */
    private function verifyWay(Battlefield $battlefield, int $x, int $y) : bool
    {
        $cell = BattlefieldModel::getCellByCoordinates($battlefield, $x, $y);

        return null !== $cell && $cell->getState()->getId() === CellModel::STATE_SHIP_DIED;
    }
}