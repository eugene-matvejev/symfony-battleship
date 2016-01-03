<?php

namespace GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\Interfaces\IdentifiableInterface;
use GameBundle\Library\Traits\Identifiable;

/**
 * Battlefield
 *
 * @ORM\Table(
 *     name="battlefields",
 *     indexes={
 *          @ORM\Index(name="INDEX_BATTLEFIELD_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_BATTLEFIELD_PLAYER", columns={"player"})
 *     })
 * @ORM\Entity(repositoryClass="GameBundle\Repository\BattlefieldRepository")
 */
class Battlefield implements IdentifiableInterface
{
    use Identifiable;
    /**
     * @ORM\OneToMany(targetEntity="GameBundle\Entity\Cell", mappedBy="battlefield", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="battlefield", nullable=false)
     *
     * @var ArrayCollection|Cell[]
     */
    private $cells;
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\Game")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    private $game;
    /**
     * @ORM\ManyToOne(targetEntity="GameBundle\Entity\Player")
     * @ORM\JoinColumn(name="player", referencedColumnName="id", nullable=false)
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