<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory\Service;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 7.0
 */
trait PathProcessorMockTrait
{
    private function getPathProcessorMock(Cell $cell) : PathProcessor
    {
        return new PathProcessor($cell);
    }
}
