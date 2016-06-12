<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractFlaggedEntity;
use JMS\Serializer\Annotation as Serializer;

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
 * @Serializer\AccessorOrder(order="custom", custom={"id", "flag", "name"})
 * @Serializer\XmlRoot("player")
 */
class Player extends AbstractFlaggedEntity
{
    /**
     * @ORM\Column(name="name", type="string", length=25)
     *
     * @Serializer\Type("string")
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
