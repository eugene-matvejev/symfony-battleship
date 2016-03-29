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
     * @see     CoordinateService::calculateNextCoordinate
     * @test
     *
     * @depends primaryWays
     * @depends extendedWays
     */
    public function calculateNextCoordinate()
    {
        /**
         *  T-L    T   T-R
         *   L < cell > R
         *  D-L    D   D-R
         */
        $patterns = [
            /** @see CoordinateService::ALL_BASIC_WAYS */
            ['way' => CoordinateService::WAY_LEFT, 'expected' => 'C4'],
            ['way' => CoordinateService::WAY_RIGHT, 'expected' => 'E4'],
            ['way' => CoordinateService::WAY_UP, 'expected' => 'D3'],
            ['way' => CoordinateService::WAY_DOWN, 'expected' => 'D5'],
            /** @see CoordinateService::WAY_NONE */
            ['way' => CoordinateService::WAY_NONE, 'expected' => 'D4'],
            /** @see CoordinateService::ALL_WAYS */
            ['way' => CoordinateService::WAY_LEFT_UP, 'expected' => 'C3'],
            ['way' => CoordinateService::WAY_LEFT_DOWN, 'expected' => 'C5'],
            ['way' => CoordinateService::WAY_RIGHT_UP, 'expected' => 'E3'],
            ['way' => CoordinateService::WAY_RIGHT_DOWN, 'expected' => 'E5']
        ];

        foreach ($patterns as $pattern) {
            $this->validateWay($pattern['way'], $pattern['expected'], 'D4');
        }
    }

    /**
     * @see     CoordinateService::getAdjacentCells
     * @test
     *
     * @depends primaryWays
     */
    public function getAdjacentCells()
    {
        $battlefield = $this->getBattlefieldMock();
        $cells = $this->getCoordinateServiceMock($battlefield->getCellByCoordinate('B2'))->getAdjacentCells();

        $this->assertCount(4, $cells);
        $this->assertEquals('A2', $cells[0]->getCoordinate());
        $this->assertEquals('C2', $cells[1]->getCoordinate());
        $this->assertEquals('B1', $cells[2]->getCoordinate());
        $this->assertEquals('B3', $cells[3]->getCoordinate());
    }

    private function validateWay(int $wayId, string $expectedCoordinate, string $startCoordinate)
    {
        $service = $this->getCoordinateServiceMock($this->getCellMock($startCoordinate));
        $service
            ->setWay($wayId)
            ->calculateNextCoordinate();
        $this->assertEquals($expectedCoordinate, $service->getValue());
    }
}
