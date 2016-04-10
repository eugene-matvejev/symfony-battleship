<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Service\CoordinateSystem\PathProcessor;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;
use EM\Tests\PHPUnit\Environment\MockFactory\Service\PathProcessorMockTrait;

/**
 * @see PathProcessor
 */
class PathProcessorTest extends ExtendedTestSuite
{
    use PathProcessorMockTrait, BattlefieldMockTrait;

    /**
     * @see PathProcessor::PRIMARY_PATHS
     * @test
     */
    public function primaryWays()
    {
        $this->assertCount(4, PathProcessor::PRIMARY_PATHS);

        $this->assertContains(PathProcessor::PATH_LEFT, PathProcessor::PRIMARY_PATHS);
        $this->assertContains(PathProcessor::PATH_RIGHT, PathProcessor::PRIMARY_PATHS);
        $this->assertContains(PathProcessor::PATH_UP, PathProcessor::PRIMARY_PATHS);
        $this->assertContains(PathProcessor::PATH_DOWN, PathProcessor::PRIMARY_PATHS);
    }

    /**
     * @see PathProcessor::EXTENDED_PATHS
     * @test
     */
    public function extendedWays()
    {
        $this->assertCount(8, PathProcessor::EXTENDED_PATHS);

        $this->assertContains(PathProcessor::PATH_LEFT, PathProcessor::EXTENDED_PATHS);
        $this->assertContains(PathProcessor::PATH_RIGHT, PathProcessor::EXTENDED_PATHS);
        $this->assertContains(PathProcessor::PATH_UP, PathProcessor::EXTENDED_PATHS);
        $this->assertContains(PathProcessor::PATH_DOWN, PathProcessor::EXTENDED_PATHS);

        $this->assertContains(PathProcessor::PATH_LEFT_UP, PathProcessor::EXTENDED_PATHS);
        $this->assertContains(PathProcessor::PATH_LEFT_DOWN, PathProcessor::EXTENDED_PATHS);
        $this->assertContains(PathProcessor::PATH_RIGHT_UP, PathProcessor::EXTENDED_PATHS);
        $this->assertContains(PathProcessor::PATH_RIGHT_DOWN, PathProcessor::EXTENDED_PATHS);

        $this->assertNotContains(PathProcessor::PATH_NONE, PathProcessor::EXTENDED_PATHS);
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedWays
     */
    public function getAdjacentCells()
    {
        $battlefield = $this->getBattlefieldMock();
        $cells = $this->getPathProcessorMock($battlefield->getCellByCoordinate('B2'))->getAdjacentCells();

        $this->assertCount(8, $cells);
        $this->assertEquals('A2', $cells[0]->getCoordinate());
        $this->assertEquals('C2', $cells[1]->getCoordinate());
        $this->assertEquals('B1', $cells[2]->getCoordinate());
        $this->assertEquals('B3', $cells[3]->getCoordinate());

        $this->assertEquals('A1', $cells[4]->getCoordinate());
        $this->assertEquals('A3', $cells[5]->getCoordinate());
        $this->assertEquals('C1', $cells[6]->getCoordinate());
        $this->assertEquals('C3', $cells[7]->getCoordinate());
    }

    /**
     * @see     PathProcessor::getNextCoordinate
     * @test
     *
     * @depends extendedWays
     */
    public function getNextCoordinate()
    {
        $expectedCoordinatesByWay = [
            PathProcessor::PATH_LEFT       => 'C4',
            PathProcessor::PATH_RIGHT      => 'E4',
            PathProcessor::PATH_UP         => 'D3',
            PathProcessor::PATH_DOWN       => 'D5',
            PathProcessor::PATH_NONE       => 'D4',
            PathProcessor::PATH_LEFT_UP    => 'C3',
            PathProcessor::PATH_LEFT_DOWN  => 'C5',
            PathProcessor::PATH_RIGHT_UP   => 'E3',
            PathProcessor::PATH_RIGHT_DOWN => 'E5'
        ];

        $service = $this->getPathProcessorMock($this->getCellMock('D4'));
        foreach ($expectedCoordinatesByWay as $wayId => $expectedCoordinate) {
            $service->setPath($wayId);
            $this->assertEquals('D4', $service->getCurrentCoordinate());
            $this->assertEquals($expectedCoordinate, $service->getNextCoordinate());
            $this->assertEquals($expectedCoordinate, $service->getCurrentCoordinate());
        }
    }
}
