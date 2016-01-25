<?php

namespace EM\GameBundle\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
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
        foreach ($battlefield->getCells() as $cell) {
            if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED || $this->isShipDead($cell)) {
                continue;
            }

            $this->logger->addDebug(__FUNCTION__ . ':: X STRATEGY');
            if ($cells = $this->xStrategy($cell)) {
                $this->logger->addDebug('>>> X');
            }

            $this->logger->addDebug(__FUNCTION__ . ':: Y STRATEGY');
            if (empty($cells) && $cells = $this->yStrategy($cell)) {
                $this->logger->addDebug('>>> Y');
            }

            $this->logger->addDebug(__FUNCTION__ . ':: Z STRATEGY');
            if (empty($cells) && $cells = $this->zStrategy($cell)) {
                $this->logger->addDebug('>>> Z');
            }

            if (!empty($cells)) {
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

        foreach ($coordinates as $coordinate) {
            if (null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinate['x'], $coordinate['y'])) {

                if (in_array($_cell->getState()->getId(), CellModel::getLiveStates())) {
                    $cells[] = $_cell;
                }
            }
        }

        return $cells;
    }

    /**
     * @param Battlefield $battlefield
     * @param array $coordinates
     * @param string $axis
     *
     * @return Cell[]
     */
    private function strategy(Battlefield $battlefield, array $coordinates, string $axis) : array
    {
        $cells = [];

        for ($i = 0; $i < $this->maxShipSize; $i++) {
            foreach ($coordinates as $i => $coordinate) {
                if (null !== $cell = CellModel::getByCoordinates($battlefield, $coordinate['x'], $coordinate['y'])) {

                    if (in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
                        $cells[$i] = $cell;
                        unset($coordinates[$i]);
                    } elseif (0 === $i) {
                        $coordinates[$i][$axis]++;
                    } elseif (1 === $i) {
                        $coordinates[$i][$axis]--;
                    }
                } else {
                    unset($coordinates[$i]);
                }
            }
        }

        return $cells;
    }

    public function isShipDead(Cell $cell) : bool
    {
        return $this->isShipIsDead($cell, 'x') || $this->isShipIsDead($cell, 'y');
        return $this->verifyShipByAxis($cell, 'x') || $this->verifyShipByAxis($cell, 'y');
    }

    public function isShipIsDead(Cell $cell, string $axis) : bool
    {
        if($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
            return false;
        }

        $coordinates = [
            ['x' => $cell->getX() - ('x' === $axis ? 1 : 0), 'y' => $cell->getY() - ('y' === $axis ? 1 : 0)],
            ['x' => $cell->getX() + ('x' === $axis ? 1 : 0), 'y' => $cell->getY() + ('y' === $axis ? 1 : 0)]
        ];

        $cells = [$cell];
        $down = $up = true;
        for($i = 0; $i < $this->maxShipSize; $i++) {
            if(true === $down && null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y'])) {
                if(in_array($_cell->getState()->getId(), [CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_LIVE])) {
                    if($_cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                        return false;
                    }
                    $cells[] = $_cell;
                } else $down = false;
            }
            if(true === $up && null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y'])) {
                if(in_array($_cell->getState()->getId(), [CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_LIVE])) {
                    if($_cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                        return false;
                    }
                    $cells[] = $_cell;
                } else $up = false;
            }
        }

        if(false === $down && false === $up) {
            $this->logger->addEmergency(__FUNCTION__ .': MARK_AS_SKIPPED axis: '. $axis .' cells: '. count($cells));
            /** @var Cell $_cell
             *  x-1; y-1 | x ; y-1 | x+1; y-1
             *  x-1;   y | x ; y   | x+1; y
             *  x-1; y+1 | x ; y+1 | x+1; y+1
             */
            $steps = [-1, 0, 1];
            foreach($cells as $_cell) {
                $coordinates = [];

                for($x = 0; $x < 3; $x++) {
                    for($y = 0; $y < 3; $y++) {
                        $coordinates[] = [
                            'x' => $_cell->getX() + $steps[$x],
                            'y' => $_cell->getY() + $steps[$y]
                        ];
                    }
                }

                foreach($coordinates as $coordinate) {
                    $this->logger->addEmergency(__FUNCTION__ .': ###MARK_AS_SKIPPED_: '. print_r($coordinates, true));
                    if(null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinate['x'], $coordinate['y'])) {
                        $this->cellModel->switchStateToSkipped($_cell);
                    }
                }
            }

            return true;
        }

        return false;
    }

    private function verifyShipByAxis(Cell $cell, string $axis) : bool
    {
        $coordinates = [
            ['x' => $cell->getX() - ('x' === $axis ? 1 : 0), 'y' => $cell->getY() - ('y' === $axis ? 1 : 0)],
            ['x' => $cell->getX() + ('x' === $axis ? 1 : 0), 'y' => $cell->getY() + ('y' === $axis ? 1 : 0)]
        ];

        $leftCell = $rightCell = true;
        $cells = [$cell];
        $matches = 1;

        for($i = 0; $i < $this->maxShipSize; $i++) {
            if(true === $leftCell && true === $leftCell = $this->verifyWay($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y'])) {
                $cells[] = CellModel::getByCoordinates($cell->getBattlefield(), $coordinates[0]['x'], $coordinates[0]['y']);
                $coordinates[0][$axis]--;
                $matches++;
            }
            if(true === $rightCell && true === $rightCell = $this->verifyWay($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y'])) {
                $cells[] = CellModel::getByCoordinates($cell->getBattlefield(), $coordinates[1]['x'], $coordinates[1]['y']);
                $coordinates[1][$axis]++;
                $matches++;
            }
        }

        if(true === $leftCell && true === $rightCell || $matches >= $this->maxShipSize) {
            $this->logger->addEmergency(__FUNCTION__ .': MARK_AS_SKIPPED axis: '. $axis .' cells: '. count($cells));
            /** @var Cell $_cell
             *  x-1; y-1 | x ; y-1 | x+1; y-1
             *  x-1;   y | x ; y   | x+1; y
             *  x-1; y+1 | x ; y+1 | x+1; y+1
             */
            $steps = [-1, 0, 1];
            foreach($cells as $_cell) {
                $coordinates = [];

                for($x = 0; $x < 3; $x++) {
                    for($y = 0; $y < 3; $y++) {
                        $coordinates[] = [
                            'x' => $_cell->getX() + $steps[$x],
                            'y' => $_cell->getY() + $steps[$y]
                        ];
                    }
                }

                foreach($coordinates as $coordinate) {
                    $this->logger->addEmergency(__FUNCTION__ .': ###MARK_AS_SKIPPED_: '. print_r($coordinates, true));
                    if(null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinate['x'], $coordinate['y'])) {
                        $this->cellModel->switchStateToSkipped($_cell);
                    }
                }
            }

            return true;
        }

        return false;
    }

    private function verifyWay(Battlefield $battlefield, int $x, int $y) : bool
    {
        $cell = CellModel::getByCoordinates($battlefield, $x, $y);

        return null !== $cell && $cell->getState()->getId() === CellModel::STATE_SHIP_DIED;
    }
}