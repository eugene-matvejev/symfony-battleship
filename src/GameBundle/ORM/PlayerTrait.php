<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\Entity\Player;
use JMS\Serializer\Annotation as Serializer;

/**
 * @since 2.0
 */
trait PlayerTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Player", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="player", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Type("EM\GameBundle\Entity\Player")
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
