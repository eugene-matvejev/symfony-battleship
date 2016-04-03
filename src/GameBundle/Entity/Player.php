<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\NameableInterface;
use EM\GameBundle\ORM\NameableTrait;
use JMS\Serializer\Annotation as JMS;

//EM\GameBundle\Entity\Player:
//    accessor_order: custom
//    custom_accessor_order: [id, type, name]
//
//    xml_root_name: player
//
//    properties:
//        id:
//            type: integer
//        type:
//            type: EM\GameBundle\Entity\PlayerType
//        name:
//            type: string
//            xml_element:
//                cdata: false


/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="players")
 *
 * @JMS\XmlRoot("player")
 * @JMS\AccessorOrder("custom", custom = {"id", "type", "name"})
 */
class Player extends AbstractEntity implements NameableInterface
{
    use NameableTrait;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\PlayerType", fetch="EAGER")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     *
     * @JMS\Type("EM\GameBundle\Entity\PlayerType")
     *
     * @var PlayerType
     */
    private $type;

    public function getType() : PlayerType
    {
        return $this->type;
    }

    public function setType(PlayerType $type) : self
    {
        $this->type = $type;

        return $this;
    }
}
