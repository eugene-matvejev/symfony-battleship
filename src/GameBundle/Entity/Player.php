<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\NameableInterface;
use EM\GameBundle\ORM\NameableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player extends AbstractEntity implements NameableInterface
{
    use NameableTrait;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\PlayerType", fetch="EAGER")
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
