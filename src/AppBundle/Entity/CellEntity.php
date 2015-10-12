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
class CellEntity
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
     * @ORM\ManyToOne(targetEntity="CellStateEntity")
     * @ORM\JoinColumn(name="state", referencedColumnName="id")
     *
     * @var integer
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="BattlefieldEntity")
     * @ORM\JoinColumn(name="battlefield", referencedColumnName="id")
     *
     * @var integer
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
     * @return CellStateEntity
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param CellStateEntity $state
     *
     * @return $this
     */
    public function setState(CellStateEntity $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return int
     */
    public function getBattlefield()
    {
        return $this->battlefield;
    }

    /**
     * @param BattlefieldEntity $battlefield
     *
     * @return $this
     */
    public function setBattlefield(BattlefieldEntity $battlefield)
    {
        $this->battlefield = $battlefield;

        return $this;
    }
}