<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\Model\CellModel;
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
     * @ORM\Column(name="coordinate", type="string", length=3)
     *
     * @var string
     */
    private $coordinate;
    /**
     * @ORM\Column(name="mask", type="integer")
     *
     * @var int
     */
    private $mask = CellModel::MASK_NONE;

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

    public function addMask(int $mask) : self
    {
        $this->mask |= $mask;

        return $this;
    }

    public function removeMask(int $mask) : self
    {
        $this->mask &= ~$mask;

        return $this;
    }

    public function setMask(int $mask) : self
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask() : int
    {
        return $this->mask;
    }

    public function hasMask(int $mask) : bool
    {
        $asd = ($this->mask & $mask) === $mask;

        return $asd;
    }
}
