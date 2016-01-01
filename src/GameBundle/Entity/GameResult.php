<?php

namespace GameBundle\Entity;

use GameBundle\Library\Traits\Identifiable;
use GameBundle\Library\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * GameResult
 *
 * @ORM\Table(
 *     name="gamesResults",
 *     indexes={
 *          @ORM\Index(name="INDEX_GAME_RESULT_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_GAME_RESULT_WINNER", columns={"winner"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="GameBundle\Repository\GameResultRepository")
 */
class GameResult
{
    use Identifiable, Timestampable;
    /**
     * @ORM\OneToOne(targetEntity="GameBundle\Entity\Game", mappedBy="result")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    private $game;
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\Player", inversedBy="id")
     * @ORM\JoinColumn(name="winner", referencedColumnName="id", nullable=false)
     *
     * @var Player
     */
    private $winner;

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

    /**
     * @return Player
     */
    public function getWinner() : Player
    {
        return $this->winner;
    }

    /**
     * @param Player $player
     *
     * @return $this
     */
    public function setWinner(Player $player)
    {
        $this->winner = $player;

        return $this;
    }
}