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
        $expectedCoordinatesByWay = [
            CoordinateService::WAY_LEFT       => 'C4',
            CoordinateService::WAY_RIGHT      => 'E4',
            CoordinateService::WAY_UP         => 'D3',
            CoordinateService::WAY_DOWN       => 'D5',
            CoordinateService::WAY_NONE       => 'D4',
            CoordinateService::WAY_LEFT_UP    => 'C3',
            CoordinateService::WAY_LEFT_DOWN  => 'C5',
            CoordinateService::WAY_RIGHT_UP   => 'E3',
            CoordinateService::WAY_RIGHT_DOWN => 'E5'
        ];

        $service = $this->getCoordinateServiceMock($this->getCellMock('D4'));
        foreach ($expectedCoordinatesByWay as $wayId => $expectedCoordinate) {
            $this->assertEquals($expectedCoordinate, $service->setWay($wayId)->getNextCoordinate());
        }
    }
}
