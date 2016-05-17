<?php

namespace EM\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Entity\Cell;

/**
 * @since 11.0
 */
class PathProcessor
{
    const PATH_NONE       = 0x00;
    const PATH_LEFT       = 0x01;
    const PATH_RIGHT      = 0x02;
    const PATH_UP         = 0x10;
    const PATH_DOWN       = 0x20;
    const PATH_LEFT_UP    = self::PATH_LEFT | self::PATH_UP;
    const PATH_LEFT_DOWN  = self::PATH_LEFT | self::PATH_DOWN;
    const PATH_RIGHT_UP   = self::PATH_RIGHT | self::PATH_UP;
    const PATH_RIGHT_DOWN = self::PATH_RIGHT | self::PATH_DOWN;
    const PRIMARY_PATHS   = [
        self::PATH_LEFT,
        self::PATH_RIGHT,
        self::PATH_UP,
        self::PATH_DOWN
    ];
    const EXTENDED_PATHS  = [
        self::PATH_LEFT,
        self::PATH_RIGHT,
        self::PATH_UP,
        self::PATH_DOWN,
        self::PATH_LEFT_UP,
        self::PATH_LEFT_DOWN,
        self::PATH_RIGHT_UP,
        self::PATH_RIGHT_DOWN
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
    private $way = self::PATH_NONE;

    public function __construct(Cell $cell)
    {
        $this->cell = $cell;
        $this->coordinate = $cell->getCoordinate();
    }

    public function setPath(int $way) : self
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
        $number = substr($this->coordinate, 1);
        $letter = substr($this->coordinate, 0, 1);
        $prevLetter = chr(ord($letter) - 1);

        /**
         * LEFT-UP   (--letter, --number)  UP (--number)  RIGHT-UP   (++letter, --number)
         * LEFT      (--letter)               current     RIGHT      (++letter)
         * LEFT-DOWN (--letter, ++number) DOWN (++number) RIGHT-DOWN (++letter, ++number)
         */
        switch ($this->way) {
            case self::PATH_UP:
                return $this->coordinate = $letter . --$number;
            case self::PATH_DOWN:
                return $this->coordinate = $letter . ++$number;
            case self::PATH_LEFT:
                return $this->coordinate = $prevLetter . $number;
            case self::PATH_LEFT_UP:
                return $this->coordinate = $prevLetter . --$number;
            case self::PATH_LEFT_DOWN:
                return $this->coordinate = $prevLetter . ++$number;
            case self::PATH_RIGHT:
                return $this->coordinate = ++$letter . $number;
            case self::PATH_RIGHT_UP:
                return $this->coordinate = ++$letter . --$number;
            case self::PATH_RIGHT_DOWN:
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
        $battlefield = $this->cell->getBattlefield();
        foreach (self::EXTENDED_PATHS as $way) {
            $this->setPath($way);

            if (null !== $cell = $battlefield->getCellByCoordinate($this->getNextCoordinate())) {
                $cells[] = $cell;
            }
        }

        return $cells;
    }
}
