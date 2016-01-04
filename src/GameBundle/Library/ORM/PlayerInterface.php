<?php

namespace GameBundle\Library\ORM;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Entity\Player;

/**
 * @since 3.1
 */
interface PlayerInterface
{
    public function getPlayer() : Player;

    public function setPlayer(Player $player);
}