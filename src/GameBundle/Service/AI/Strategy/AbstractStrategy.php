<?php

namespace EM\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;

/**
 * @since 3.5
 */
abstract class AbstractStrategy
{
    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    abstract public function verify(Cell $cell) : array;

    /**
     * @param Battlefield         $battlefield
     * @param CoordinateService[] $coordinates
     *
     * @return Cell[]
     */
    protected function verifyByCoordinates(Battlefield $battlefield, array $coordinates) : array
    {
        $cells = [];
        foreach ($coordinates as $coordinate) {
            $coordinate->calculateNextCoordinate();

            while ($cell = $battlefield->getCellByCoordinate($coordinate->getValue())) {
                $coordinate->calculateNextCoordinate();

                if (in_array($cell->getState()->getId(), CellModel::STATES_LIVE)) {
                    $cells[] = $cell;
                    break;
                }
            }
        }

        return $cells;
    }
}
