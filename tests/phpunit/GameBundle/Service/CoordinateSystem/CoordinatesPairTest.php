<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see CoordinatesPair
 */
class CoordinatesPairTest extends ExtendedTestCase
{
    const COORDINATE_X = 2;
    const COORDINATE_Y = 2;

    /**
     * @see CoordinatesPair::prepareForNextStep
     * @test
     */
    public function wayLeft()
    {
        $coordinatesPair = $this->getMockedCoordinatesPair(CoordinatesPair::WAY_LEFT);
        $this->assertSame(self::COORDINATE_X, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y, $coordinatesPair->getY());
        $coordinatesPair->prepareForNextStep();
        $this->assertSame(self::COORDINATE_X - 1, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y, $coordinatesPair->getY());
    }

    /**
     * @see CoordinatesPair::prepareForNextStep
     * @test
     */
    public function wayRight()
    {
        $coordinatesPair = $this->getMockedCoordinatesPair(CoordinatesPair::WAY_RIGHT);
        $this->assertSame(self::COORDINATE_X, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y, $coordinatesPair->getY());
        $coordinatesPair->prepareForNextStep();
        $this->assertSame(self::COORDINATE_X + 1, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y, $coordinatesPair->getY());
    }

    /**
     * @see CoordinatesPair::prepareForNextStep
     * @test
     */
    public function wayUp()
    {
        $coordinatesPair = $this->getMockedCoordinatesPair(CoordinatesPair::WAY_UP);
        $this->assertSame(self::COORDINATE_X, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y, $coordinatesPair->getY());
        $coordinatesPair->prepareForNextStep();
        $this->assertSame(self::COORDINATE_X, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y - 1, $coordinatesPair->getY());
    }

    /**
     * @see CoordinatesPair::prepareForNextStep
     * @test
     */
    public function wayDown()
    {
        $coordinatesPair = $this->getMockedCoordinatesPair(CoordinatesPair::WAY_DOWN);
        $this->assertSame(self::COORDINATE_X, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y, $coordinatesPair->getY());
        $coordinatesPair->prepareForNextStep();
        $this->assertSame(self::COORDINATE_X, $coordinatesPair->getX());
        $this->assertSame(self::COORDINATE_Y + 1, $coordinatesPair->getY());
    }

    /**
     * @param int $way
     *
     * @return CoordinatesPair
     *
     * @coversNothing
     */
    protected function getMockedCoordinatesPair(int $way) : CoordinatesPair
    {
        return new CoordinatesPair($way, self::COORDINATE_X, self::COORDINATE_Y);
    }
}
