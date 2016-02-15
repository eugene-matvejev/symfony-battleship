<?php

namespace EM\GameBundle\Service\CoordinateSystem;

/**
 * @since 3.4
 */
class CoordinatesPair
{
    const WAY_LEFT  = 1;
    const WAY_RIGHT = 2;
    const WAY_UP    = 3;
    const WAY_DOWN  = 4;
    /**
     * @var int
     */
    private $x;
    /**
     * @var int
     */
    private $y;
    /**
     * @var int
     */
    private $way;

    public function __construct(int $way, int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->way = $way;
    }

    public function getX() : int
    {
        return $this->x;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function prepareForNextStep()
    {
        switch ($this->way) {
            case self::WAY_LEFT:
                $this->x--;
                break;
            case self::WAY_RIGHT:
                $this->x++;
                break;
            case self::WAY_UP:
                $this->y--;
                break;
            case self::WAY_DOWN:
                $this->y++;
                break;
        }
    }
}
