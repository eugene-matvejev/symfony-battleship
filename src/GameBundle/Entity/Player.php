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