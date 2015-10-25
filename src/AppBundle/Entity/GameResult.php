<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
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
    use Identifiable;

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
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private $timestamp;

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

    /**
     * @ORM\PrePersist
     */
    public function setTimestamp()
    {
        $this->timestamp = new \DateTime();
    }
}