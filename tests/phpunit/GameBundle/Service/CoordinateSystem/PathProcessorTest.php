<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see PathProcessor
 */
class PathProcessorTest extends IntegrationTestSuite
{
    /**
     * primary paths are: UP, DOWN, LEFT, RIGHT
     *
     * @see PathProcessor::$primaryPaths
     * @test
     */
    public function primaryPaths()
    {
        $this->iteratePaths(PathProcessor::$primaryPaths, [
            PathProcessor::PATH_LEFT,
            PathProcessor::PATH_RIGHT,
            PathProcessor::PATH_UP,
            PathProcessor::PATH_DOWN
        ]);
    }

    /**
     * extended paths list contains all paths from @see PathProcessor::$primaryPaths and 4 additional: (LEFT|RIGHT)-(UP|DOWN)
     *
     * @see PathProcessor::$extendedPaths
     * @test
     */
    public function extendedPaths()
    {
        $this->iteratePaths(PathProcessor::$extendedPaths, [
            PathProcessor::PATH_LEFT,
            PathProcessor::PATH_RIGHT,
            PathProcessor::PATH_UP,
            PathProcessor::PATH_DOWN,
            PathProcessor::PATH_LEFT_UP,
            PathProcessor::PATH_LEFT_DOWN,
            PathProcessor::PATH_RIGHT_UP,
            PathProcessor::PATH_RIGHT_DOWN
        ]);
    }

    private function iteratePaths(array $actualPaths, array $expectedPaths)
    {
        $this->assertCount(count($expectedPaths), $actualPaths);

        foreach ($expectedPaths as $path) {
            $this->assertContains($path, $actualPaths);
        }
    }

    /**
     * check entire list of paths to contain directions
     *
     * @see     PathProcessor::hasDirection
     * @test
     *
     * @depends primaryPaths
     * @depends extendedPaths
     */
    public function hasDirectionYes()
    {
        $fixtures = [
            PathProcessor::PATH_LEFT       => [PathProcessor::PATH_LEFT],
            PathProcessor::PATH_RIGHT      => [PathProcessor::PATH_RIGHT],
            PathProcessor::PATH_UP         => [PathProcessor::PATH_UP],
            PathProcessor::PATH_DOWN       => [PathProcessor::PATH_DOWN],
            PathProcessor::PATH_NONE       => [],
            PathProcessor::PATH_LEFT_UP    => [PathProcessor::PATH_LEFT, PathProcessor::PATH_UP],
            PathProcessor::PATH_LEFT_DOWN  => [PathProcessor::PATH_LEFT, PathProcessor::PATH_DOWN],
            PathProcessor::PATH_RIGHT_UP   => [PathProcessor::PATH_RIGHT, PathProcessor::PATH_UP],
            PathProcessor::PATH_RIGHT_DOWN => [PathProcessor::PATH_RIGHT, PathProcessor::PATH_DOWN]
        ];

        foreach ($fixtures as $path => $directions) {
            /** should return true if full direction matches full-direction */
            $this->assertTrue($this->hasDirection($path, $path));

            foreach ($directions as $direction) {
                /** should return true if full direction contains direction */
                $this->assertTrue($this->hasDirection($path, $direction));
            }
        }
    }

    /**
     * check entire list of paths to do not contain directions
     *
     * @see     PathProcessor::hasDirection
     * @test
     *
     * @depends primaryPaths
     * @depends extendedPaths
     */
    public function hasDirectionNot()
    {
        $fixtures = [
            PathProcessor::PATH_LEFT       => [PathProcessor::PATH_RIGHT],
            PathProcessor::PATH_RIGHT      => [PathProcessor::PATH_LEFT],
            PathProcessor::PATH_UP         => [PathProcessor::PATH_DOWN],
            PathProcessor::PATH_DOWN       => [PathProcessor::PATH_UP],
            PathProcessor::PATH_NONE       => PathProcessor::$extendedPaths,
            PathProcessor::PATH_LEFT_UP    => [PathProcessor::PATH_RIGHT, PathProcessor::PATH_DOWN],
            PathProcessor::PATH_LEFT_DOWN  => [PathProcessor::PATH_RIGHT, PathProcessor::PATH_UP],
            PathProcessor::PATH_RIGHT_UP   => [PathProcessor::PATH_LEFT, PathProcessor::PATH_DOWN],
            PathProcessor::PATH_RIGHT_DOWN => [PathProcessor::PATH_LEFT, PathProcessor::PATH_UP],
        ];

        foreach ($fixtures as $path => $directions) {
            foreach ($directions as $direction) {
                $this->assertFalse($this->hasDirection($path, $direction));
            }
        }
    }

    /**
     * @see PathProcessor::hasDirection
     * 
     * @param int $path
     * @param int $bytes
     *
     * @return mixed
     */
    private function hasDirection(int $path, int $bytes)
    {
        return $this->invokeMethod((new PathProcessor('B2'))->setPath($path), 'hasDirection', [$bytes]);
    }

    /**
     * @see PathProcessor::reset
     * @test
     */
    public function resetWithDefaults()
    {
        $processor = new PathProcessor('B2');
        $processor
            ->setPath(PathProcessor::PATH_DOWN)
            ->getNextCoordinate();

        $this->assertNotEquals($processor->getCurrentCoordinate(), $processor->getOriginCoordinate());

        $processor->reset();
        $this->assertEquals($processor->getCurrentCoordinate(), 'B2');
        $this->assertEquals($processor->getCurrentCoordinate(), $processor->getOriginCoordinate());
    }

    /**
     * @see PathProcessor::reset
     * @test
     */
    public function resetWithCoordinate()
    {
        $processor = new PathProcessor('B2');
        $processor
            ->setPath(PathProcessor::PATH_DOWN)
            ->getNextCoordinate();

        $this->assertNotEquals($processor->getCurrentCoordinate(), $processor->getOriginCoordinate());

        $processor->reset('B3');
        $this->assertEquals($processor->getCurrentCoordinate(), 'B3');
        $this->assertEquals($processor->getCurrentCoordinate(), $processor->getOriginCoordinate());
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsWithDefaults()
    {
        $this->iterateAdjacentCells(
            ['A1', 'A2', 'A3', 'B1', 'B3', 'C1', 'C2', 'C3'],
            (new PathProcessor('B2'))->getAdjacentCells(MockFactory::getBattlefieldMock())
        );
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsWithDefaults2LevelDeep()
    {
        $this->iterateAdjacentCells(
            ['A1', 'A2', 'A3', 'B1', 'B3', 'B4', 'C1', 'C2', 'C3', 'D2', 'D4'],
            (new PathProcessor('B2'))->getAdjacentCells(MockFactory::getBattlefieldMock(), 2)
        );
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsOnlyDeadCells()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD);
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);

        $this->iterateAdjacentCells(
            ['A1', 'A2'],
            (new PathProcessor('B2'))->getAdjacentCells($battlefield, 1, CellModel::FLAG_DEAD)
        );
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsOnlyLiveCells()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD);
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);

        $this->iterateAdjacentCells(
            ['A3', 'B1', 'B3', 'C1', 'C2', 'C3'],
            (new PathProcessor('B2'))->getAdjacentCells($battlefield, 1, CellModel::FLAG_NONE, CellModel::FLAG_DEAD)
        );
    }

    private function iterateAdjacentCells(array $expectedCoordinates, array $cells)
    {
        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(count($expectedCoordinates), $cells);

        foreach ($expectedCoordinates as $coordinate) {
            $this->assertArrayHasKey($coordinate, $cells);
            $this->assertEquals($coordinate, $cells[$coordinate]->getCoordinate());
        }
    }

    /**
     * should:
     *      find next coordinate by path
     *      save it to save it as processor's current coordinate
     *
     * @see     PathProcessor::getNextCoordinate
     * @test
     *
     * @depends extendedPaths
     */
    public function getNextCoordinate()
    {
        $expectedCoordinateByPath = [
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

        $processor = new PathProcessor('D4');
        foreach ($expectedCoordinateByPath as $path => $expectedCoordinate) {
            $processor->setPath($path);

            $this->assertEquals($expectedCoordinate, $processor->getNextCoordinate());
            $this->assertEquals($expectedCoordinate, $processor->getCurrentCoordinate());
        }
    }
}
