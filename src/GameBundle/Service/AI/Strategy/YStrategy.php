<?php

namespace EM\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;

/**
 * @since 3.5
 */
class YStrategy extends AbstractStrategy
{
    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    public function verify(Cell $cell) : array
    {
        $service = new CoordinateService($cell);

        return $this->verifyByCoordinates($cell->getBattlefield(), [
            clone $service->setWay(CoordinateService::WAY_UP),
            clone $service->setWay(CoordinateService::WAY_DOWN)
        ]);
    }
}
