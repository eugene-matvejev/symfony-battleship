<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\ORM\IdentifiableInterface;
use GameBundle\Library\ORM\NameableInterface;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\NameableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player implements IdentifiableInterface, NameableInterface
{
    use IdentifiableTrait, NameableTrait;
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\PlayerType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=false)
     *
     * @var PlayerType
     */
    private $type;

    /**
     * @return PlayerType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param PlayerType $type
     *
     * @return $this
     */
    public function setType(PlayerType $type)
    {
        $this->type = $type;

        return $this;
    }
}