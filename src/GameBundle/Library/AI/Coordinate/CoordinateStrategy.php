<?php

namespace GameBundle\Library\AI\Coordinate;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Model\BattlefieldModel;
use GameBundle\Model\CellModel;
use Symfony\Bridge\Monolog\Logger;

class CoordinateStrategy
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
        $this->logger->addCritical(__FUNCTION__ .': count: '. count($battlefield->getCells()));
        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                continue;
            }
            if($this->isShipDead($cell)) {
                continue;
            }

            $this->logger->addCritical(__FUNCTION__ .': cell: '. $cell->getId());

            $this->logger->addCritical('X STRATEGY');
            if($cells = $this->xStrategy($cell)) {
                $this->logger->addCritical('>>> X');
                if(!empty($cells))
                    return $cells;
            }

            $this->logger->addCritical('Y STRATEGY');
            if($cells = $this->yStrategy($cell)) {
                $this->logger->addCritical('>>> Y');
                if(!empty($cells))
                    return $cells;
            }

            $this->logger->addCritical('Z STRATEGY');
            if($cells = $this->zStrategy($cell)) {
                $this->logger->addCritical('>>> Z');
                if(!empty($cells))
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
    private function xStrategy(Cell $cell)
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
    private function yStrategy(Cell $cell)
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
                $this->logger->addCritical(__FUNCTION__ .': cell: '. $cell->getId() .' state: '. $cell->getState()->getId());
                $this->logger->addCritical(__FUNCTION__ .': x'. $cell->getX() .' y:'. $cell->getY());
                if(in_array($_cell->getState()->getId(), CellModel::getLiveStates()))
                    $cells[] = $_cell;
            }
        }

        return $cells;
    }

    /**
     * @param Battlefield $battlefield
     * @param array       $coordinates
     * @param string|null $axis
     *
     * @return Cell[]
     */
    private function strategy(Battlefield $battlefield, array $coordinates, string $axis) : array
    {
        $cells = [];
        $this->logger->addCritical(':::: '. __FUNCTION__ .' :::::::::::: '. print_r($coordinates, true));
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

    /**
     * @param Cell $cell
     *
     * @return bool
     */
    private function isShipDead(Cell $cell)
    {
        $xCoordinates = [
            ['x' => $cell->getX() - 1, 'y' => $cell->getY()],
            ['x' => $cell->getX() + 1, 'y' => $cell->getY()]
        ];
        if(true !== $this->verifyShipByAxis($cell, $xCoordinates, 'x')) {
            return true;
        }
        $yCoordinates = [
            ['x' => $cell->getX(), 'y' => $cell->getY() - 1],
            ['x' => $cell->getX(), 'y' => $cell->getY() + 1]
        ];

        return $this->verifyShipByAxis($cell, $yCoordinates, 'y');
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
//        $cells = [];
        $leftCell = $rightCell = true;
        $this->logger->addCritical(__FUNCTION__ .'::::'. print_r($coordinates, true));

        //[0] => Array         (             [x] => -1            [y] => 1         )
        //[1] => Array         (             [x] => 1             [y] => 1         )
        $matches = 1;

        for($i = 0; $i < $this->maxShipSize; $i++) {
            if(true === $leftCell && true === $leftCell = $this->verifyWay($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y'])) {
                $matches++;
            }
            if(true === $rightCell && true === $rightCell = $this->verifyWay($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y'])) {
                $matches++;
            }
//
//            if($matches >= $this->maxShipSize) {
//                $this->logger->addCritical(__FUNCTION__ .': '. $axis .'0true');
//                return true;
//            }

            $coordinates[0][$axis]--;
            $coordinates[1][$axis]++;
        }

        if(true === $leftCell && true === $rightCell || $matches >= $this->maxShipSize) {
            $this->logger->addCritical(__FUNCTION__ .': '. $axis .'1true');
//            /**
//             * @var Cell[] $cells
//             */
//            foreach($cells as $_cell) {
//                $coordinates = [
//                    [
//                        'x' => $_cell->getX(),
//                        'y' => $_cell->getY()
//                    ],
//                    [
//                        'x' => $_cell->getX(),
//                        'y' => $_cell->getY()
//                    ]
//                ];
//
//                $coordinates[0][$axis]--;
//                $coordinates[1][$axis]++;
//
//                $cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y']);
//                if(null !== $cell) {
//                    $this->cellModel->markAsSkipped($_cell1);
//                }
//                $cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y']);
//                if(null !== $cell) {
//                    $this->cellModel->markAsSkipped($_cell2);
//                }
//            }
            return true;
        }

        return false;
    }

    /**
     * @param Battlefield $bf
     * @param int         $x
     * @param int         $y
     *
     * @return bool
     */
    private function verifyWay(Battlefield $bf, int $x, int $y) : bool
    {
        $cell = BattlefieldModel::getCellByCoordinates($bf, $x, $y);

        return null !== $cell && $cell->getState()->getId() === CellModel::STATE_SHIP_DIED;
    }
}