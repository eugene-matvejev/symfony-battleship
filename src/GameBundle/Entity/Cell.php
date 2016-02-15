<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\IdentifiableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="cells",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="axisXY", columns={"battlefield", "x", "y"})
 *      },
 *      indexes={
 *          @ORM\Index(name="INDEX_CELL_BATTLEFIELD", columns={"battlefield"})
 *      }
 * )
 */
class Cell implements IdentifiableInterface
{
    use IdentifiableTrait;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Battlefield", inversedBy="cells")
     * @ORM\JoinColumn(name="battlefield", referencedColumnName="id", nullable=false)
     *
     * @var Battlefield
     */
    private $battlefield;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\CellState")
     * @ORM\JoinColumn(name="state", referencedColumnName="id", nullable=false)
     *
     * @var CellState
     */
    private $state;
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

    public function getBattlefield() : Battlefield
    {
        return $this->battlefield;
    }

    public function setBattlefield(Battlefield $battlefield) : self
    {
        $this->battlefield = $battlefield;

        return $this;
    }

    public function getState() : CellState
    {
        return $this->state;
    }

    public function setState(CellState $state) : self
    {
        $this->state = $state;

        return $this;
    }

    public function getX() : int
    {
        return $this->x;
    }

    public function setX(int $x) : self
    {
        $this->x = $x;

        return $this;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function setY(int $y) : self
    {
        $this->y = $y;

        return $this;
    }
}
