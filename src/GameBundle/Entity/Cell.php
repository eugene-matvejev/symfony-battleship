<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="cells",
 *      indexes={
 *          @ORM\Index(name="INDEX_CELLS_BATTLEFIELD", columns={"battlefield"})
 *      },
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNIQUE_CELL_PER_BATTLEFIELD", columns={"battlefield", "coordinate"})
 *      }
 * )
 */
class Cell extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Battlefield", inversedBy="cells", fetch="EAGER")
     * @ORM\JoinColumn(name="battlefield", referencedColumnName="id", nullable=false)
     *
     * @var Battlefield
     */
    private $battlefield;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\CellState", fetch="EAGER")
     * @ORM\JoinColumn(name="state", referencedColumnName="id", nullable=false)
     *
     * @var CellState
     */
    private $state;
    /**
     * @ORM\Column(name="coordinate", type="string", nullable=false, length=3)
     *
     * @var string
     */
    private $coordinate;

    public function getBattlefield() : Battlefield
    {
        return $this->battlefield;
    }

    public function setBattlefield(Battlefield $battlefield) : self
    {
        $this->battlefield = $battlefield;

        return $this;
    }

    public function getCoordinate() : string
    {
        return $this->coordinate;
    }

    public function setCoordinate(string $coordinate) : self
    {
        $this->coordinate = $coordinate;

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
}
