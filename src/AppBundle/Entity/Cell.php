<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Cell
 *
 * @ORM\Table(name="cells", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="axisXY", columns={"battlefield", "x", "y"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CellRepository")
 */
class Cell
{
    use Identifiable;

    /**
     * @ORM\Column(name="x", type="integer", nullable=false)
     *
     * @var integer
     */
    private $x;

    /**
     * @ORM\Column(name="y", type="integer", nullable=false)
     *
     * @var integer
     */
    private $y;

    /**
     * @ORM\ManyToOne(targetEntity="CellState")
     * @ORM\JoinColumn(name="state", referencedColumnName="id")
     *
     * @var CellState
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="Battlefield")
     * @ORM\JoinColumn(name="battlefield", referencedColumnName="id")
     *
     * @var Battlefield
     */
    private $battlefield;

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param int $x
     *
     * @return $this
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param int $y
     *
     * @return $this
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * @return CellState
     */
    public function getState()
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
    public function getBattlefield()
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