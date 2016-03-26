<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Service\CoordinateSystem\CoordinateService;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\CellMockTrait;
use EM\Tests\PHPUnit\Environment\MockFactory\Service\CoordinateServiceMockTrait;

/**
 * @see CoordinateService
 */
class CoordinateServiceTest extends ExtendedTestCase
{
    use CoordinateServiceMockTrait, CellMockTrait;

    /**
     * @see CoordinateService::ALL_WAYS
     * @test
     */
    public function allWays()
    {
        $this->assertCount(8, CoordinateService::ALL_WAYS);

        $this->assertContains(CoordinateService::WAY_LEFT, CoordinateService::ALL_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT, CoordinateService::ALL_WAYS);
        $this->assertContains(CoordinateService::WAY_UP, CoordinateService::ALL_WAYS);
        $this->assertContains(CoordinateService::WAY_DOWN, CoordinateService::ALL_WAYS);

        $this->assertContains(CoordinateService::WAY_LEFT_UP, CoordinateService::ALL_WAYS);
        $this->assertContains(CoordinateService::WAY_LEFT_DOWN, CoordinateService::ALL_WAYS);

        $this->assertContains(CoordinateService::WAY_RIGHT_UP, CoordinateService::ALL_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT_DOWN, CoordinateService::ALL_WAYS);

        $this->assertNotContains(CoordinateService::WAY_NONE, CoordinateService::ALL_WAYS);
    }

    /**
     * @see CoordinateService::ALL_BASIC_WAYS
     * @test
     */
    public function basicWays()
    {
        $this->assertCount(4, CoordinateService::ALL_BASIC_WAYS);

        $this->assertContains(CoordinateService::WAY_LEFT, CoordinateService::ALL_BASIC_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT, CoordinateService::ALL_BASIC_WAYS);
        $this->assertContains(CoordinateService::WAY_UP, CoordinateService::ALL_BASIC_WAYS);
        $this->assertContains(CoordinateService::WAY_DOWN, CoordinateService::ALL_BASIC_WAYS);
    }

    /**
     * @see CoordinateService::STRATEGY_X
     * @test
     */
    public function xWays()
    {
        $this->assertCount(2, CoordinateService::STRATEGY_X);

        $this->assertContains(CoordinateService::WAY_LEFT, CoordinateService::ALL_BASIC_WAYS);
        $this->assertContains(CoordinateService::WAY_RIGHT, CoordinateService::ALL_BASIC_WAYS);
    }

    /**
     * @see CoordinateService::STRATEGY_Y
     * @test
     */
    public function yWays()
    {
        $this->assertCount(2, CoordinateService::STRATEGY_Y);

        $this->assertContains(CoordinateService::WAY_UP, CoordinateService::ALL_BASIC_WAYS);
        $this->assertContains(CoordinateService::WAY_DOWN, CoordinateService::ALL_BASIC_WAYS);
    }

    /**
     * @see CoordinateService::calculateNextCoordinate
     * @test
     */
    public function calculateNextCoordinate()
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

        foreach ($patterns as $pattern) {
            $this->validateWay($pattern['way'], $pattern['expected'], 'D4');
        }
    }

    private function validateWay(int $wayId, string $expectedCoordinate, string $startCoordinate)
    {
        $service = $this->getCoordinateServiceMock($this->getCellMock($startCoordinate));
        $service->setWay($wayId);
        $service->calculateNextCoordinate();
        $this->assertEquals($expectedCoordinate, $service->getValue());
    }
}
