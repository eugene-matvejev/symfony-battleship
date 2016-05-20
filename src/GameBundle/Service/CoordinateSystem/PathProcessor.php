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
     * @var int
     */
    private $path = self::PATH_NONE;
    /**
     * @var string
     */
    private $currentCoordinate;

    public function __construct(Cell $cell)
    {
        $this->cell = $cell;
        $this->currentCoordinate = $cell->getCoordinate();
    }

    public function setPath(int $path) : self
    {
        $this->path = $path;
        $this->currentCoordinate = $this->cell->getCoordinate();

        return $this;
    }

    public function getCurrentCoordinate() : string
    {
        return $this->currentCoordinate;
    }

    /**
     * LEFT-UP   (--letter, --number)  UP (--number)  RIGHT-UP   (++letter, --number)
     * LEFT      (--letter)               UNCHANGED   RIGHT      (++letter)
     * LEFT-DOWN (--letter, ++number) DOWN (++number) RIGHT-DOWN (++letter, ++number)
     */
    public function getNextCoordinate() : string
    {
        $number = substr($this->currentCoordinate, 1);
        $letter = substr($this->currentCoordinate, 0, 1);

        if (($this->path & static::PATH_UP) === static::PATH_UP) {
            --$number;
        } elseif (($this->path & static::PATH_DOWN) === static::PATH_DOWN) {
            ++$number;
        }

        if (($this->path & static::PATH_RIGHT) === static::PATH_RIGHT) {
            ++$letter;
        } elseif (($this->path & static::PATH_LEFT) === static::PATH_LEFT) {
            $letter = chr(ord($letter) - 1);
        }

        return $this->currentCoordinate = $letter . $number;
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
