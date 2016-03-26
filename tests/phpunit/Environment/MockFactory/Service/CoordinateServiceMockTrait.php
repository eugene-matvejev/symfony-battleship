<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory;

use EM\GameBundle\Service\CoordinateSystem\CoordinateService;

/**
 * @since 7.0
 */
trait CoordinateServiceMockTrait
{
    private function getCoordinateServiceMock(Cell $cell) : CoordinateService
    {
        return new CoordinateService($cell);
    }
}
