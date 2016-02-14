<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\IdentifiableTrait;
use EM\GameBundle\ORM\NameableInterface;
use EM\GameBundle\ORM\NameableTrait;

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
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\PlayerType")
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