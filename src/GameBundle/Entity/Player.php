<?php

namespace EM\FoundationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\ORM\AbstractFlaggedEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="players",
 *      indexes={
 *          @ORM\Index(name="INDEX_PLAYER_NAME", columns={"name"})
 *      }
 * )
 *
 * @JMS\AccessorOrder(order="custom", custom={"id", "flag", "name"})
 * @JMS\XmlRoot("player")
 */
class Player extends AbstractFlaggedEntity
{
    /**
     * @ORM\Column(name="name", type="string", length=25)
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $name;

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }
}
