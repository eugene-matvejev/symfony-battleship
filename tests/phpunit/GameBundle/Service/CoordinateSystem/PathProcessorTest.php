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
     * primary paths are only: UP, DOWN, LEFT, RIGHT
     *
     * @see PathProcessor::PRIMARY_PATHS
     * @test
     */
    public function primaryPaths()
    {
        $this->assertCount(4, PathProcessor::PRIMARY_PATHS);

        $expectedPaths = [
            PathProcessor::PATH_LEFT,
            PathProcessor::PATH_RIGHT,
            PathProcessor::PATH_UP,
            PathProcessor::PATH_DOWN
        ];

        foreach ($expectedPaths as $path) {
            $this->assertContains($path, PathProcessor::PRIMARY_PATHS);
        }
    }

    /**
     * extended paths contains primary paths as well as (LEFT|RIGHT)-(UP|DOWN)
     *
     * @see PathProcessor::EXTENDED_PATHS
     * @test
     */
    public function extendedPaths()
    {
        $this->assertCount(8, PathProcessor::EXTENDED_PATHS);

        $expectedPaths = [
            PathProcessor::PATH_LEFT,
            PathProcessor::PATH_RIGHT,
            PathProcessor::PATH_UP,
            PathProcessor::PATH_DOWN,
            PathProcessor::PATH_LEFT_UP,
            PathProcessor::PATH_LEFT_DOWN,
            PathProcessor::PATH_RIGHT_UP,
            PathProcessor::PATH_RIGHT_DOWN
        ];

        foreach ($expectedPaths as $path) {
            $this->assertContains($path, PathProcessor::EXTENDED_PATHS);
        }
    }

    /**
     * check entire list of paths to contain bytes
     *
     * @see     PathProcessor::isPathContainsBytes
     * @test
     *
     * @depends primaryPaths
     * @depends extendedPaths
     */
    public function isPathContainsBytesYes()
    {
        $contains = [
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

        foreach ($contains as $path => $set) {
            $this->assertTrue($this->invokeIsPathContainsBytesMethod($path, $path));

            foreach ($set as $bytes) {
                $this->assertTrue($this->invokeIsPathContainsBytesMethod($path, $bytes));
            }
        }
    }

    /**
     * check entire list of paths to do not contain bytes into path
     *
     * @see     PathProcessor::isPathContainsBytes
     * @test
     *
     * @depends primaryPaths
     * @depends extendedPaths
     */
    public function isPathContainsBytesNot()
    {
        $notContains = [
            PathProcessor::PATH_LEFT       => [PathProcessor::PATH_RIGHT],
            PathProcessor::PATH_RIGHT      => [PathProcessor::PATH_LEFT],
            PathProcessor::PATH_UP         => [PathProcessor::PATH_DOWN],
            PathProcessor::PATH_DOWN       => [PathProcessor::PATH_UP],
            PathProcessor::PATH_NONE       => PathProcessor::EXTENDED_PATHS,
            PathProcessor::PATH_LEFT_UP    => [PathProcessor::PATH_RIGHT, PathProcessor::PATH_DOWN],
            PathProcessor::PATH_LEFT_DOWN  => [PathProcessor::PATH_RIGHT, PathProcessor::PATH_UP],
            PathProcessor::PATH_RIGHT_UP   => [PathProcessor::PATH_LEFT, PathProcessor::PATH_DOWN],
            PathProcessor::PATH_RIGHT_DOWN => [PathProcessor::PATH_LEFT, PathProcessor::PATH_UP],
        ];

        foreach ($notContains as $path => $set) {
            $this->assertTrue($this->invokeIsPathContainsBytesMethod($path, $path));

            foreach ($set as $bytes) {
                $this->assertFalse($this->invokeIsPathContainsBytesMethod($path, $bytes));
            }
        }
    }

    /**
     * @param int $path
     * @param int $bytes
     *
     * @return mixed
     */
    private function invokeIsPathContainsBytesMethod(int $path, int $bytes)
    {
        $processor = (new PathProcessor(MockFactory::getCellMock('B2')))
            ->setPath($path);

        return $this->invokeMethod($processor, 'isPathContainsBytes', [$bytes]);
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsNoFlags()
    {
        $cells = (new PathProcessor(MockFactory::getBattlefieldMock()->getCellByCoordinate('B2')))->getAdjacentCells();

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(8, $cells);

        foreach (['A2', 'C2', 'B1', 'B3', 'A1', 'A3', 'C1', 'C3'] as $coordinate) {
            $this->assertArrayHasKey($coordinate, $cells);
            $this->assertEquals($coordinate, $cells[$coordinate]->getCoordinate());
        }
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsWith_FLAG_DEAD_OnNotDeadCells()
    {
        $cells = (new PathProcessor(MockFactory::getBattlefieldMock()->getCellByCoordinate('B2')))->getAdjacentCells(CellModel::FLAG_DEAD);

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(8, $cells);

        foreach (['A2', 'C2', 'B1', 'B3', 'A1', 'A3', 'C1', 'C3'] as $coordinate) {
            $this->assertArrayHasKey($coordinate, $cells);
            $this->assertEquals($coordinate, $cells[$coordinate]->getCoordinate());
        }
    }

    /**
     * @see     PathProcessor::getAdjacentCells
     * @test
     *
     * @depends extendedPaths
     */
    public function getAdjacentCellsWith_FLAG_DEAD_OnSomeDeadCells()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $fixtures = [
            'A2' => CellModel::FLAG_DEAD,
            'C2' => CellModel::FLAG_SHIP,
            'B1' => CellModel::FLAG_SKIP,
            'B3' => CellModel::FLAG_DEAD_SHIP
        ];

        foreach ($fixtures as $coordinate => $flag) {
            $battlefield->getCellByCoordinate($coordinate)->setFlags($flag);
        }

        $cells = (new PathProcessor($battlefield->getCellByCoordinate('B2')))->getAdjacentCells(CellModel::FLAG_DEAD);

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(5, $cells);

        foreach (['C2', 'A1', 'A3', 'C1', 'C3'] as $coordinate) {
            $this->assertArrayHasKey($coordinate, $cells);
            $this->assertEquals($coordinate, $cells[$coordinate]->getCoordinate());
        }
    }

    /**
     * @see     PathProcessor::getNextCoordinate
     * @test
     *
     * @depends extendedPaths
     */
    public function getNextCoordinate()
    {
        $expectedCoordinatesByPath = [
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

        $processor = new PathProcessor(MockFactory::getCellMock('D4'));
        foreach ($expectedCoordinatesByPath as $path => $expectedCoordinate) {
            $processor->setPath($path);

            $this->assertEquals('D4', $processor->getCurrentCoordinate());

            $this->assertEquals($expectedCoordinate, $processor->getNextCoordinate());
            $this->assertEquals($expectedCoordinate, $processor->getCurrentCoordinate());

            /** if PathProcessor::PATH_NONE then coordinate should not change */
            if (PathProcessor::PATH_NONE !== $path) {
                $this->assertNotEquals($expectedCoordinate, $processor->getNextCoordinate());
                $this->assertNotEquals($expectedCoordinate, $processor->getCurrentCoordinate());
            } else {
                $this->assertEquals('D4', $processor->getCurrentCoordinate());
            }
        }
    }
}
