<?php

namespace GameBundle\Library\ORM;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Entity\Game;

trait GameTrait
{
    /**
     * @ORM\OneToOne(targetEntity="GameBundle\Entity\Game", mappedBy="result")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    private $game;

    /**
     * @return Game
     */
    public function getGame() : Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     *
     * @return $this
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }
}
