<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractFlaggedEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * EM\GameBundle\Entity\Player:
 * accessor_order: custom
 * custom_accessor_order: [id, flag, name]
 *
 * xml_root_name: player
 *
 * properties:
 * id:
 * type: integer
 * flag:
 * type: integer
 * name:
 * type: string
 */

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
     * @ORM\Column(name="name", type="string", length=100)
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
