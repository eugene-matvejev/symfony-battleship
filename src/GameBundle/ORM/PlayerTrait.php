<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\Entity\Player;

/**
 * @since 2.0
 */
trait PlayerTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Player", cascade={"persist"})
     * @ORM\JoinColumn(name="player", referencedColumnName="id", nullable=false)
     *
     * @var Player
     */
    private $player;

    public function getPlayer() : Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player) : self
    {
        $this->player = $player;

        return $this;
    }
}
