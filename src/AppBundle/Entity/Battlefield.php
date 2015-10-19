<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Battlefield
 *
 * @ORM\Table(name="battlefields")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattlefieldRepository")
 */
class Battlefield
{
    use Identifiable;

    /**
     * @ORM\OneToMany(targetEntity="Cell", mappedBy="battlefield")
     * @ORM\JoinColumn(name="id", referencedColumnName="battlefield")
     *
     * @var Cell[]
     */
    private $cells;

    /**
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="game", referencedColumnName="id")
     *
     * @var Game
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="player", referencedColumnName="id")
     *
     * @var Player
     */
    private $player;

    /**
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * @return Game
     */
    public function getGame()
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
    public function getPlayer()
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