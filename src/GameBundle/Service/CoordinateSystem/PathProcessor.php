<?php

namespace EM\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Model\CellModel;

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
    const PRIMARY_PATHS  = [
        self::PATH_LEFT,
        self::PATH_RIGHT,
        self::PATH_UP,
        self::PATH_DOWN
    ];
    const EXTENDED_PATHS = [
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
     * @var int
     */
    private $path;
    /**
     * @var string
     */
    private $originCoordinate;
    /**
     * @var string
     */
    private $currentCoordinate;

    public function __construct(string $coordinate)
    {
        $this->setOriginCoordinate($coordinate);
    }

    public function getOriginCoordinate() : string
    {
        return $this->originCoordinate;
    }

    public function setOriginCoordinate(string $coordinate) : self
    {
        $this->currentCoordinate = $this->originCoordinate = $coordinate;
        $this->path = static::PATH_NONE;

        return $this;
    }

    /**
     * sets path and reset current coordinate to origin
     *
     * @param int $path
     *
     * @return PathProcessor
     */
    public function setPath(int $path) : self
    {
        $this->path = $path;
        $this->currentCoordinate = $this->originCoordinate;

        return $this;
    }

    public function getCurrentCoordinate() : string
    {
        return $this->currentCoordinate;
    }

    /**
     * calculate next coordinate by path and set it as current coordinate and return it
     *
     * LEFT-UP   (--letter, --number)  UP (--number)  RIGHT-UP   (++letter, --number)
     * LEFT      (--letter)               UNCHANGED   RIGHT      (++letter)
     * LEFT-DOWN (--letter, ++number) DOWN (++number) RIGHT-DOWN (++letter, ++number)
     *
     * @return string
     */
    public function getNextCoordinate() : string
    {
        $number = substr($this->currentCoordinate, 1);
        $letter = substr($this->currentCoordinate, 0, 1);

        if ($this->isPathContainsBytes(static::PATH_UP)) {
            --$number;
        } elseif ($this->isPathContainsBytes(static::PATH_DOWN)) {
            ++$number;
        }

        if ($this->isPathContainsBytes(static::PATH_RIGHT)) {
            ++$letter;
        } elseif ($this->isPathContainsBytes(static::PATH_LEFT)) {
            $letter = chr(ord($letter) - 1);
        }

        return $this->currentCoordinate = $letter . $number;
    }

    /**
     * @param Battlefield $battlefield
     * @param int|null    $excludeFlag [optional] - cells with this flag will be ignored
     *
     * @return Cell[]
     */
    public function getAdjacentCells(Battlefield $battlefield, int $excludeFlag = CellModel::FLAG_NONE) : array
    {
        $cells = [];
        foreach (self::EXTENDED_PATHS as $path) {
            $this
                ->setPath($path)
                ->getNextCoordinate();

            try {
                $cell = $this->getCellByCurrentCoordinate($battlefield, $excludeFlag);
            } catch (CellException $e) {
                continue;
            }

            $cells[$cell->getCoordinate()] = $cell;
        }

        return $cells;
    }

    /**
     * @param Battlefield $battlefield
     * @param int         $excludeFlag - cells with this flag will be ignored
     *
     * @return Cell
     * @throws CellException
     */
    protected function getCellByCurrentCoordinate(Battlefield $battlefield, int $excludeFlag) : Cell
    {
        if (null !== $cell = $battlefield->getCellByCoordinate($this->currentCoordinate)) {
            if ($excludeFlag && $cell->hasFlag($excludeFlag)) {
                throw new CellException("{$cell->getId()} had $excludeFlag");
            }

            return $cell;
        }

        throw new CellException("{$battlefield->getId()} do not contain cell with coordinate: {$this->currentCoordinate}");
    }

    protected function isPathContainsDirection(int $flag) : bool
    {
        return ($this->path & $flag) === $flag;
    }
}
