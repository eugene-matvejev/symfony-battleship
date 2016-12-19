<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\ORM\AbstractFlaggedEntity;
use JMS\Serializer\Annotation as JMS;

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
 *
 * @JMS\AccessorOrder(order="custom", custom={"id", "coordinate", "flags"})
 * @JMS\XmlRoot("cell")
 */
class Cell extends AbstractFlaggedEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Battlefield", inversedBy="cells", fetch="EAGER")
     * @ORM\JoinColumn(name="battlefield", referencedColumnName="id", nullable=false)
     *
     * @JMS\Exclude()
     *
     * @var Battlefield
     */
    protected $battlefield;
    /**
     * @ORM\Column(name="coordinate", type="string", length=3)
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $coordinate;

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
}
