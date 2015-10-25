<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * GameResult
 *
 * @ORM\Table(name="gamesResults")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameResultRepository")
 */
class GameResult
{
    use Identifiable, Timestampable;

    /**
     * @ORM\OneToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="game", referencedColumnName="id")
     *
     * @var Game
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="winner", referencedColumnName="id")
     *
     * @var Player
     */
    private $winner;

    /**
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param $game
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
    public function getWinner()
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