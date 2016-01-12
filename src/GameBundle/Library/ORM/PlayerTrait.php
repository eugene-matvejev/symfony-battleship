<?php

namespace GameBundle\Library\ORM;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Entity\Player;

/**
 * @since 3.1
 */
trait PlayerTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\Player")
     * @ORM\JoinColumn(name="player", referencedColumnName="id", nullable=false)
     *
     * @var Player
     */
    private $player;

    /**
     * @return Player
     */
    public function getPlayer() : Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     *
     * @return $this
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;

        return $this;
    }
}
