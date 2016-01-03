<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\Interfaces\IdentifiableInterface;
use GameBundle\Library\ORM\IdentifiableTrait;

/**
 * Cell
 *
 * @ORM\Table(
 *      name="cells",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="axisXY", columns={"battlefield", "x", "y"})
 *      },
 *      indexes={
 *          @ORM\Index(name="INDEX_CELL_BATTLEFIELD", columns={"battlefield"})
 *      }
 * )
 * @ORM\Entity()
 */
class Cell implements IdentifiableInterface
{
    use IdentifiableTrait;
    /**
     * @ORM\Column(name="x", type="integer", nullable=false)
     *
     * @var int
     */
    private $x;
    /**
     * @ORM\Column(name="y", type="integer", nullable=false)
     *
     * @var int
     */
    private $y;
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\CellState")
     * @ORM\JoinColumn(name="state", referencedColumnName="id", nullable=false)
     *
     * @var CellState
     */
    private $state;
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\Battlefield", inversedBy="id")
     * @ORM\JoinColumn(name="battlefield", referencedColumnName="id", nullable=false)
     *
     * @var Battlefield
     */
    private $battlefield;

    /**
     * @return int
     */
    public function getX() : int
    {
        return $this->x;
    }

    /**
     * @param int $x
     *
     * @return $this
     */
    public function setX(int $x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * @return int
     */
    public function getY() : int
    {
        return $this->y;
    }

    /**
     * @param int $y
     *
     * @return $this
     */
    public function setY(int $y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * @return CellState
     */
    public function getState() : CellState
    {
        return $this->state;
    }

    /**
     * @param CellState $state
     *
     * @return $this
     */
    public function setState(CellState $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Battlefield
     */
    public function getBattlefield() : Battlefield
    {
        return $this->battlefield;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return $this
     */
    public function setBattlefield(Battlefield $battlefield)
    {
        $this->battlefield = $battlefield;

        return $this;
    }
}