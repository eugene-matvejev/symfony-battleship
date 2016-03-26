<?php

namespace EM\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\IdentifiableTrait;
use EM\GameBundle\ORM\PlayerInterface;
use EM\GameBundle\ORM\PlayerTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="battlefields",
 *     indexes={
 *          @ORM\Index(name="INDEX_BATTLEFIELD_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_BATTLEFIELD_PLAYER", columns={"player"})
 *     })
 */
class Battlefield implements IdentifiableInterface, PlayerInterface
{
    use IdentifiableTrait, PlayerTrait;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Game", inversedBy="battlefields", fetch="EAGER")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    private $game;
    /**
     * @ORM\OneToMany(targetEntity="EM\GameBundle\Entity\Cell", mappedBy="battlefield", cascade={"persist"}, fetch="EAGER", indexBy="coordinate")
     *
     * @var Collection|Cell[]
     */
    private $cells;

    public function __construct()
    {
        $this->cells = new ArrayCollection();
    }

    public function getGame() : Game
    {
        return $this->game;
    }

    public function setGame(Game $game) : self
    {
        $this->game = $game;

        return $this;
    }

    public function addCell(Cell $cell) : self
    {
        $cell->setBattlefield($this);
        $this->cells->set($cell->getCoordinate(), $cell);

        return $this;
    }

    public function removeCell(Cell $cell) : self
    {
        $this->cells->removeElement($cell);

        return $this;
    }

    /**
     * @return Collection|Cell[]
     */
    public function getCells() : Collection
    {
        return $this->cells;
    }

    /**
     * @param string $coordinate
     *
     * @return Cell|null
     */
    public function getCellByCoordinate(string $coordinate)
    {
        return $this->cells->get($coordinate);
    }
}
