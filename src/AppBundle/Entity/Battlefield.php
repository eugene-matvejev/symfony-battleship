<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cell", mappedBy="battlefield", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="battlefield")
     *
     * @var ArrayCollection|Cell[]
     */
    private $cells;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Game")
     * @ORM\JoinColumn(name="game")
     *
     * @var Game
     */
    private $game;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player")
     * @ORM\JoinColumn(name="player")
     *
     * @var Player
     */
    private $player;

    public function __construct()
    {
        $this->cells = new ArrayCollection();
    }

    /**
     * @param Cell $cell
     *
     * @return $this
     */
    public function addCell(Cell $cell)
    {
        $cell->setBattlefield($this);
        $this->cells->add($cell);

        return $this;
    }

    /**
     * @param Cell $cell
     *
     * @return $this
     */
    public function removeCell(Cell $cell)
    {
        $this->cells->removeElement($cell);

        return $this;
    }

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