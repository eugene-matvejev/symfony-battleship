<?php

namespace EM\FoundationBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\Entity\Player;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 2.0
 */
trait PlayerTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="EM\FoundationBundle\Entity\Player", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="player", referencedColumnName="id", nullable=false)
     *
     * @JMS\Type("EM\FoundationBundle\Entity\Player")
     *
     * @var Player
     */
    protected $player;

    public function getPlayer() : Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     *
     * @return $this
     */
    public function setPlayer(Player $player) : self
    {
        $this->player = $player;

        return $this;
    }
}
