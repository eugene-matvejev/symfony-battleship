<?php

namespace EM\GameBundle\Service\CoordinateSystem;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Model\CellModel;

/**
 * @see   PathProcessorTest
 *
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
    public static $primaryPaths = [
        self::PATH_LEFT,
        self::PATH_RIGHT,
        self::PATH_UP,
        self::PATH_DOWN
    ];
    public static $extendedPaths = [
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
        $this->reset($coordinate);
    }

    public function getOriginCoordinate() : string
    {
        return $this->originCoordinate;
    }

    /**
     * @since 19.0
     *
     * @param string|null $coordinate
     *
     * @return PathProcessor
     */
    public function reset(string $coordinate = null) : self
    {
        $this->currentCoordinate = $this->originCoordinate = $coordinate ?? $this->originCoordinate;
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
        $this->reset();
        $this->path = $path;

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
        return $this->currentCoordinate = $this->getCoordinateLetterPart() . $this->getCoordinateNumberPart();
    }

    protected function getCoordinateNumberPart() : int
    {
        $number = substr($this->currentCoordinate, 1);

        if ($this->hasDirection(static::PATH_UP)) {
            --$number;
        } elseif ($this->hasDirection(static::PATH_DOWN)) {
            ++$number;
        }

        return $number;
    }

    protected function getCoordinateLetterPart() : string
    {
        $letter = substr($this->currentCoordinate, 0, 1);

        if ($this->hasDirection(static::PATH_RIGHT)) {
            ++$letter;
        } elseif ($this->hasDirection(static::PATH_LEFT)) {
            $letter = chr(ord($letter) - 1);
        }

        return $letter;
    }

    /**
     * @since 18.3
     *
     * @param int $flag
     *
     * @return bool
     */
    protected function hasDirection(int $flag) : bool
    {
        return ($this->path & $flag) === $flag;
    }

    /**
     * @param Battlefield $battlefield
     * @param int         $levels      [optional] - how many levels to check
     * @param int|null    $onlyFlag    [optional] - cells only with this flag will be returned
     * @param int|null    $excludeFlag [optional] - cells with this flag will be ignored
     *
     * @return Cell[]
     */
    public function getAdjacentCells(Battlefield $battlefield, int $levels = 1, int $onlyFlag = CellModel::FLAG_NONE, int $excludeFlag = CellModel::FLAG_NONE) : array
    {
        $cells = [];
        foreach (static::$extendedPaths as $path) {
            $this->setPath($path);

            for ($i = 0; $i < $levels; $i++) {
                $this->getNextCoordinate();

                try {
                    $cell = $this->resolveCellGetter($battlefield, $onlyFlag, $excludeFlag);
                } catch (CellException $e) {
                    break;
                }

                $cells[$cell->getCoordinate()] = $cell;
            }
        }

        return $cells;
    }

    /**
     * @since 21.2
     *
     * @param Battlefield $battlefield
     * @param int         $flag
     * @param int         $levels
     *
     * @return array
     */
    public function getFlaggedAdjacentCells(Battlefield $battlefield, int $flag, int $levels = 1) : array
    {
        return $this->getAdjacentCells($battlefield, $levels, $flag);
    }

    /**
     * @since 21.2
     *
     * @param Battlefield $battlefield
     * @param int         $flag
     * @param int         $levels
     *
     * @return array
     */
    public function getNotFlaggedAdjacentCells(Battlefield $battlefield, int $flag, int $levels = 1) : array
    {
        return $this->getAdjacentCells($battlefield, $levels, CellModel::FLAG_NONE, $flag);
    }

    /**
     * @since 21.2
     *
     * @param Battlefield $battlefield
     * @param int         $onlyFlag
     * @param int         $excludeFlag
     *
     * @return Cell
     * @throws CellException
     */
    protected function resolveCellGetter(Battlefield $battlefield, int $onlyFlag, int $excludeFlag) : Cell
    {
        switch (true) {
            case !empty($onlyFlag):
                return $this->getFlaggedCellByCurrentCoordinate($battlefield, $onlyFlag);
            case !empty($excludeFlag):
                return $this->getNotFlaggedCellByCurrentCoordinate($battlefield, $excludeFlag);
            default:
                return $this->getCellByCurrentCoordinate($battlefield);
        }
    }

    /**
     * @since 19.0
     *
     * @param Battlefield $battlefield
     *
     * @return Cell
     * @throws CellException
     */
    protected function getCellByCurrentCoordinate(Battlefield $battlefield) : Cell
    {
        if (null !== $cell = $battlefield->getCellByCoordinate($this->currentCoordinate)) {
            return $cell;
        }

        throw new CellException("{$battlefield->getId()} do not contain cell with coordinate: {$this->currentCoordinate}");
    }

    /**
     * @since 21.2
     *
     * @param Battlefield $battlefield
     * @param int         $flag - only with this flag cells will be returned
     *
     * @return Cell
     * @throws CellException
     */
    protected function getFlaggedCellByCurrentCoordinate(Battlefield $battlefield, int $flag)
    {
        $cell = $this->getCellByCurrentCoordinate($battlefield);
        if (!$cell->hasFlag($flag)) {
            throw new CellException("{$cell->getId()} don't have mandatory flag {$flag}");
        }

        return $cell;
    }

    /**
     * @since 21.2
     *
     * @param Battlefield $battlefield
     * @param int         $flag - cells with this flag will be ignored, $onlyFlag has a priority
     *
     * @return Cell
     * @throws CellException
     */
    protected function getNotFlaggedCellByCurrentCoordinate(Battlefield $battlefield, int $flag)
    {
        $cell = $this->getCellByCurrentCoordinate($battlefield);
        if ($cell->hasFlag($flag)) {
            throw new CellException("{$cell->getId()} has {$flag}");
        }

        return $cell;
    }
}
