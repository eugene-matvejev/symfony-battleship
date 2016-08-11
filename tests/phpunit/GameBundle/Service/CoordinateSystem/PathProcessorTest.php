<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see PathProcessor
 */
class PathProcessorTest extends IntegrationTestSuite
{
    /**
     * @see          PathProcessor::$primaryPaths
     * @test
     *
     * @dataProvider primaryPathsProvider
     *
     * @param int $path
     */
    public function primaryPaths(int $path)
    {
        $this->assertContains($path, PathProcessor::$primaryPaths);
    }

    /**
     * primary paths are UP, DOWN, LEFT, RIGHT
     */
    public function primaryPathsProvider() : array
    {
        return [
            [PathProcessor::PATH_LEFT],
            [PathProcessor::PATH_RIGHT],
            [PathProcessor::PATH_UP],
            [PathProcessor::PATH_DOWN]
        ];
    }

    /**
     * @see          PathProcessor::$extendedPaths
     * @test
     *
     * @dataProvider extendedPathsProvider
     *
     * @param int $path
     */
    public function extendedPaths(int $path)
    {
        $this->assertContains($path, PathProcessor::$extendedPaths);
    }

    /**
     * extended paths list contains all paths from @see PathProcessor::$primaryPaths and 4 additional paths: (LEFT|RIGHT)-(UP|DOWN)
     */
    public function extendedPathsProvider() : array
    {
        return array_merge(
            $this->primaryPathsProvider(),
            [
                [PathProcessor::PATH_LEFT_UP],
                [PathProcessor::PATH_LEFT_DOWN],
                [PathProcessor::PATH_RIGHT_UP],
                [PathProcessor::PATH_RIGHT_DOWN]
            ]
        );
    }

    /**
     * check entire list of paths to contain directions
     *
     * @see          PathProcessor::hasDirection
     * @test
     *
     * @depends      primaryPaths
     * @depends      extendedPaths
     *
     * @dataProvider hasDirectionYesProvider
     *
     * @param int   $path
     * @param int[] $directions
     */
    public function hasDirectionYes(int $path, array $directions)
    {
        /** should return true if full direction matches full-direction */
        $this->assertTrue($this->hasDirection($path, $path));

        foreach ($directions as $direction) {
            /** should return true if full direction contains direction */
            $this->assertTrue($this->hasDirection($path, $direction));
        }
    }

    public function hasDirectionYesProvider() : array
    {
        return [
            [PathProcessor::PATH_LEFT, [PathProcessor::PATH_LEFT]],
            [PathProcessor::PATH_RIGHT, [PathProcessor::PATH_RIGHT]],
            [PathProcessor::PATH_UP, [PathProcessor::PATH_UP]],
            [PathProcessor::PATH_DOWN, [PathProcessor::PATH_DOWN]],
            [PathProcessor::PATH_NONE, []],
            [PathProcessor::PATH_LEFT_UP, [PathProcessor::PATH_LEFT, PathProcessor::PATH_UP]],
            [PathProcessor::PATH_LEFT_DOWN, [PathProcessor::PATH_LEFT, PathProcessor::PATH_DOWN]],
            [PathProcessor::PATH_RIGHT_UP, [PathProcessor::PATH_RIGHT, PathProcessor::PATH_UP]],
            [PathProcessor::PATH_RIGHT_DOWN, [PathProcessor::PATH_RIGHT, PathProcessor::PATH_DOWN]]
        ];
    }

    /**
     * check entire list of paths to do not contain directions
     *
     * @see          PathProcessor::hasDirection
     * @test
     *
     * @depends      primaryPaths
     * @depends      extendedPaths
     *
     * @dataProvider hasDirectionNotProvider
     *
     * @param int   $path
     * @param int[] $directions
     */
    public function hasDirectionNot(int $path, array $directions)
    {
        foreach ($directions as $direction) {
            $this->assertFalse($this->hasDirection($path, $direction));
        }
    }

    public function hasDirectionNotProvider() : array
    {
        return [
            [PathProcessor::PATH_LEFT, [PathProcessor::PATH_RIGHT]],
            [PathProcessor::PATH_RIGHT, [PathProcessor::PATH_LEFT]],
            [PathProcessor::PATH_UP, [PathProcessor::PATH_DOWN]],
            [PathProcessor::PATH_DOWN, [PathProcessor::PATH_UP]],
            [PathProcessor::PATH_NONE, PathProcessor::$extendedPaths],
            [PathProcessor::PATH_LEFT_UP, [PathProcessor::PATH_RIGHT, PathProcessor::PATH_DOWN]],
            [PathProcessor::PATH_LEFT_DOWN, [PathProcessor::PATH_RIGHT, PathProcessor::PATH_UP]],
            [PathProcessor::PATH_RIGHT_UP, [PathProcessor::PATH_LEFT, PathProcessor::PATH_DOWN]],
            [PathProcessor::PATH_RIGHT_DOWN, [PathProcessor::PATH_LEFT, PathProcessor::PATH_UP]]
        ];
    }

    /**
     * @see PathProcessor::reset
     * @test
     */
    public function reset()
    {
        $processor = new PathProcessor('B2');
        $processor
            ->setPath(PathProcessor::PATH_DOWN)
            ->calculateNextCoordinate();

        $processor->reset('B3');
        $this->assertEquals($processor->getCurrentCoordinate(), 'B3');
        $this->assertEquals($processor->getCurrentCoordinate(), $processor->getOriginCoordinate());
    }

    public function AdjacentCellsProvider() : array
    {

        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD);
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);

        return [
            'defaults'                                     => [
                'B2',
                ['A1', 'A2', 'A3', 'B1', 'B3', 'C1', 'C2', 'C3'],
                MockFactory::getBattlefieldMock(),
                1,
                CellModel::FLAG_NONE,
                CellModel::FLAG_NONE
            ],
            'defaults: 2 dept'                             => [
                'B2',
                ['A1', 'A2', 'A3', 'B1', 'B3', 'B4', 'C1', 'C2', 'C3', 'D2', 'D4'],
                MockFactory::getBattlefieldMock(),
                2,
                CellModel::FLAG_NONE,
                CellModel::FLAG_NONE
            ],
            'defaults: $onlyFlag: CellModel::FLAG_DEAD'    => [
                'B2',
                ['A1', 'A2'],
                $battlefield,
                1,
                CellModel::FLAG_DEAD,
                CellModel::FLAG_NONE
            ],
            'defaults: $excludeFlag: CellModel::FLAG_DEAD' => [
                'B2',
                ['A3', 'B1', 'B3', 'C1', 'C2', 'C3'],
                $battlefield,
                1,
                CellModel::FLAG_NONE,
                CellModel::FLAG_DEAD
            ]
        ];
    }

    /**
     * @see          PathProcessor::getAdjacentCells
     * @test
     *
     * @dataProvider AdjacentCellsProvider
     *
     * @param string      $initCoordinate
     * @param array       $expectedCoordinates
     * @param Battlefield $battlefield
     * @param int         $levels
     * @param int         $onlyFlag
     * @param int         $excludeFlag
     */
    public function getAdjacentCells(string $initCoordinate, array $expectedCoordinates, Battlefield $battlefield, int $levels, int $onlyFlag, int $excludeFlag)
    {
        $cells = (new PathProcessor($initCoordinate))->getAdjacentCells($battlefield, $levels, $onlyFlag, $excludeFlag);
        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(count($expectedCoordinates), $cells);

        foreach ($expectedCoordinates as $coordinate) {
            $this->assertArrayHasKey($coordinate, $cells);
            $this->assertEquals($coordinate, $cells[$coordinate]->getCoordinate());
        }
    }

    public function nextCoordinateProvider() : array
    {
        return [
            ['D4', PathProcessor::PATH_LEFT, 'C4'],
            ['D4', PathProcessor::PATH_RIGHT, 'E4'],
            ['D4', PathProcessor::PATH_UP, 'D3'],
            ['D4', PathProcessor::PATH_DOWN, 'D5'],
            ['D4', PathProcessor::PATH_NONE, 'D4'],
            ['D4', PathProcessor::PATH_LEFT_UP, 'C3'],
            ['D4', PathProcessor::PATH_LEFT_DOWN, 'C5'],
            ['D4', PathProcessor::PATH_RIGHT_UP, 'E3'],
            ['D4', PathProcessor::PATH_RIGHT_DOWN, 'E5']
        ];
    }

    /**
     * should:
     *      passed coordinate to constructor is starting current coordinate
     *      find "next coordinate "coordinate by path
     *      save "next coordinate" as current coordinate
     *
     * @see          PathProcessor::calculateNextCoordinate
     * @test
     *
     * @depends      extendedPaths
     *
     * @dataProvider nextCoordinateProvider
     *
     * @param string $startCoordinate
     * @param int    $path
     * @param string $expectedCoordinate
     */
    public function calculateNextCoordinate(string $startCoordinate, int $path, string $expectedCoordinate)
    {
        $processor = (new PathProcessor($startCoordinate))
            ->setPath($path);

        $this->assertEquals($startCoordinate, $processor->getCurrentCoordinate());
        $this->assertEquals($expectedCoordinate, $processor->calculateNextCoordinate());
        $this->assertEquals($expectedCoordinate, $processor->getCurrentCoordinate());
    }

    /**
     * @see PathProcessor::hasDirection
     *
     * @param int $path
     * @param int $direction
     *
     * @return bool
     */
    private function hasDirection(int $path, int $direction) : bool
    {
        return $this->invokeMethod((new PathProcessor('B2'))->setPath($path), 'hasDirection', [$direction]);
    }
}
