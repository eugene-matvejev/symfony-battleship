<?php

namespace GameBundle\Library\Interfaces;

use GameBundle\Entity\Game;

/**
 * @since 3.1
 */
interface GameInterface
{
    public function getGame() : Game;

    public function setGame(Game $game);
}