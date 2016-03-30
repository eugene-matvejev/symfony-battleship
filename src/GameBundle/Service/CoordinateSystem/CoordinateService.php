<?php

namespace EM\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Entity\Cell;

/**
 * @since 7.0
 */
class CoordinateService
{
    const WAY_NONE       = 0;
    const WAY_LEFT       = 1;
    const WAY_RIGHT      = 2;
    const WAY_UP         = 3;
    const WAY_DOWN       = 4;
    const WAY_LEFT_UP    = 5;
    const WAY_LEFT_DOWN  = 6;
    const WAY_RIGHT_UP   = 7;
    const WAY_RIGHT_DOWN = 8;
    const PRIMARY_WAYS   = [
        self::WAY_LEFT,
        self::WAY_RIGHT,
        self::WAY_UP,
        self::WAY_DOWN
    ];
    const EXTENDED_WAYS  = [
        self::WAY_LEFT,
        self::WAY_RIGHT,
        self::WAY_UP,
        self::WAY_DOWN,
        self::WAY_LEFT_UP,
        self::WAY_LEFT_DOWN,
        self::WAY_RIGHT_UP,
        self::WAY_RIGHT_DOWN
    ];
    /**
     * @var Cell
     */
    private $cell;
    /**
     * @var string
     */
    private $coordinate;
    /**
     * @var int
     */
    private $way = self::WAY_NONE;

    public function __construct(Cell $cell)
    {
        $this->cell = $cell;
        $this->coordinate = $cell->getCoordinate();
    }

    public function setWay(int $way) : self
    {
        $this->way = $way;
        $this->coordinate = $this->cell->getCoordinate();

        return $this;
    }

    public function getCurrentCoordinate() : string
    {
        return $this->coordinate;
    }

    public function getNextCoordinate() : string
    {
        /**
         *                    UP (digit -- )
         * LEFT (letter -- )     current     RIGHT (letter ++ )
         *                   DOWN (digit ++ )
         */
        $letter = substr($this->coordinate, 0, 1);
        $prevLetter = chr(ord($letter) - 1);
        $number = substr($this->coordinate, 1);

        switch ($this->way) {
            case self::WAY_UP:
                return $this->coordinate = $letter . --$number;
            case self::WAY_DOWN:
                return $this->coordinate = $letter . ++$number;
            case self::WAY_LEFT:
                return $this->coordinate = $prevLetter . $number;
            case self::WAY_LEFT_UP:
                return $this->coordinate = $prevLetter . --$number;
            case self::WAY_LEFT_DOWN:
                return $this->coordinate = $prevLetter . ++$number;
            case self::WAY_RIGHT:
                return $this->coordinate = ++$letter . $number;
            case self::WAY_RIGHT_UP:
                return $this->coordinate = ++$letter . --$number;
            case self::WAY_RIGHT_DOWN:
                return $this->coordinate = ++$letter . ++$number;
        }

        return $this->coordinate;
    }

    /**
     * @return Cell[]
     */
    public function getAdjacentCells() : array
    {
        $cells = [];
        foreach (self::EXTENDED_WAYS as $way) {
            $this->setWay($way);

            if (null !== $cell = $this->cell->getBattlefield()->getCellByCoordinate($this->getNextCoordinate())) {
                $cells[] = $cell;
            }
        }

        return $cells;
    }
}
