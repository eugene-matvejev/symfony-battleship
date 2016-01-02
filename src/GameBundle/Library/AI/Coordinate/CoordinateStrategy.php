<?php

namespace GameBundle\Library\AI\Coordinate;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Model\BattlefieldModel;
use GameBundle\Model\CellModel;
use Symfony\Bridge\Monolog\Logger;

class CoordinateStrategy
{
    const STRATEGY_X = 0;
    const STRATEGY_Y = 1;
    const STRATEGY_Z = 2;
    /**
     * @var int
     */
    private $minShipSize;
    /**
     * @var int
     */
    private $maxShipSize;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max, Logger $logger)
    {
        $this->minShipSize = $min;
        $this->maxShipSize = $max;
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
     * @return array
     */
    private function strategy(Battlefield $battlefield, array $coordinates, string $axis)
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
        $x = $cell->getX();
        $y = $cell->getY();
        $cell1 = $cell2 = true;

        $x1 = $x - 1;
        $x2 = $x + 1;
        $matches = 1;

        for($i = 0; $i < $this->maxShipSize; $i++) {
            if(true === $cell1 && true === $cell1 = $this->keepSearch($cell->getBattlefield(), $x1, $cell->getY())) {
                $matches++;
            }
            if(true === $cell2 && true === $cell2 = $this->keepSearch($cell->getBattlefield(), $x2, $cell->getY())) {
                $matches++;
            }

            if($matches >= $this->maxShipSize) {
                $this->logger->addCritical(__FUNCTION__ .': 0true');
                return true;
            }

            $x1--; $x2++;
        }

        if(true === $cell1 && true === $cell2) {
            $this->logger->addCritical(__FUNCTION__ .': 1true');
            return true;
        }
        $cell1 = $cell2 = true;

        $y1 = $y - 1;
        $y2 = $y + 1;

        for($i = 0; $i < $this->maxShipSize; $i++) {
            if(true === $cell1 && true === $cell1 = $this->keepSearch($cell->getBattlefield(), $cell->getX(), $y1)) {
                $matches++;
            }
            if(true === $cell2 && true === $cell2 = $this->keepSearch($cell->getBattlefield(), $cell->getX(), $y2)) {
                $matches++;
            }

            if($matches >= $this->maxShipSize) {
                $this->logger->addCritical(__FUNCTION__ .': 2true');
                return true;
            }

            $y1--; $y2++;
        }

        $this->logger->addCritical(__FUNCTION__ .': 3'. (false === $cell1 && false === $cell2 ? 'true' : 'false'));
        return true === $cell1 && true === $cell2;
    }

    /**
     * @param Battlefield $battlefield
     * @param int         $x
     * @param int         $y
     *
     * @return bool
     */
    private function keepSearch(Battlefield $battlefield, int $x, int $y) : bool
    {
        $cell = BattlefieldModel::getCellByCoordinates($battlefield, $x, $y);

        return null !== $cell && $cell->getState()->getId() === CellModel::STATE_SHIP_DIED;
    }
}