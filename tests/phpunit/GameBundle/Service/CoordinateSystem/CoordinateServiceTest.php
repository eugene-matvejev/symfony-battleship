<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Service\CoordinateSystem\CoordinateService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;
use EM\Tests\PHPUnit\Environment\MockFactory\Service\CoordinateServiceMockTrait;

/**
 * @see CoordinateService
 */
class CoordinateServiceTest extends ExtendedTestSuite
{
    use CoordinateServiceMockTrait, BattlefieldMockTrait;

    /**
     * @see CoordinateService::PRIMARY_WAYS
     * @test
     */
    public function primaryWays()
    {
        $this->assertCount(4, CoordinateService::PRIMARY_WAYS);

        $this->assertContains(CoordinateService::WAY_LEFT, CoordinateService::PRIMARY_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT, CoordinateService::PRIMARY_WAYS);
        $this->assertContains(CoordinateService::WAY_UP, CoordinateService::PRIMARY_WAYS);
        $this->assertContains(CoordinateService::WAY_DOWN, CoordinateService::PRIMARY_WAYS);
    }

    /**
     * @see CoordinateService::EXTENDED_WAYS
     * @test
     */
    public function extendedWays()
    {
        $this->assertCount(8, CoordinateService::EXTENDED_WAYS);

        $this->assertContains(CoordinateService::WAY_LEFT, CoordinateService::EXTENDED_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT, CoordinateService::EXTENDED_WAYS);
        $this->assertContains(CoordinateService::WAY_UP, CoordinateService::EXTENDED_WAYS);
        $this->assertContains(CoordinateService::WAY_DOWN, CoordinateService::EXTENDED_WAYS);

        $this->assertContains(CoordinateService::WAY_LEFT_UP, CoordinateService::EXTENDED_WAYS);
        $this->assertContains(CoordinateService::WAY_LEFT_DOWN, CoordinateService::EXTENDED_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT_UP, CoordinateService::EXTENDED_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT_DOWN, CoordinateService::EXTENDED_WAYS);

        $this->assertNotContains(CoordinateService::WAY_NONE, CoordinateService::EXTENDED_WAYS);
    }

    /**
     * @see     CoordinateService::getAdjacentCells
     * @test
     *
     * @depends extendedWays
     */
    public function getAdjacentCells()
    {
        $battlefield = $this->getBattlefieldMock();
        $cells = $this->getCoordinateServiceMock($battlefield->getCellByCoordinate('B2'))->getAdjacentCells();

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
     * @see     CoordinateService::getNextCoordinate
     * @test
     *
     * @depends primaryWays
     * @depends extendedWays
     */
    public function getNextCoordinate()
    {
        /**
         *  T-L    T   T-R
         *   L < cell > R
         *  D-L    D   D-R
         */
        $patterns = [
            ['way' => CoordinateService::WAY_LEFT, 'expected' => 'C4'],
            ['way' => CoordinateService::WAY_RIGHT, 'expected' => 'E4'],
            ['way' => CoordinateService::WAY_UP, 'expected' => 'D3'],
            ['way' => CoordinateService::WAY_DOWN, 'expected' => 'D5'],

            ['way' => CoordinateService::WAY_NONE, 'expected' => 'D4'],

            ['way' => CoordinateService::WAY_LEFT_UP, 'expected' => 'C3'],
            ['way' => CoordinateService::WAY_LEFT_DOWN, 'expected' => 'C5'],
            ['way' => CoordinateService::WAY_RIGHT_UP, 'expected' => 'E3'],
            ['way' => CoordinateService::WAY_RIGHT_DOWN, 'expected' => 'E5']
        ];

        $service = $this->getCoordinateServiceMock($this->getCellMock('D4'));
        foreach ($patterns as $pattern) {
            $this->assertEquals($pattern['expected'], $service->setWay($pattern['way'])->getNextCoordinate());
        }
    }
}
