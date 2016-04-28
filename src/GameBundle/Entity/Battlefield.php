<?php

namespace EM\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\PlayerInterface;
use EM\GameBundle\ORM\PlayerTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="battlefields",
 *     indexes={
 *          @ORM\Index(name="INDEX_BATTLEFIELDS_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_BATTLEFIELDS_PLAYER", columns={"player"})
 *     })
 */
class Battlefield extends AbstractEntity implements PlayerInterface
{
    use PlayerTrait;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Game", inversedBy="battlefields", fetch="EAGER")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    protected $game;
    /**
     * @ORM\OneToMany(targetEntity="EM\GameBundle\Entity\Cell", mappedBy="battlefield", cascade={"persist"}, fetch="EAGER", indexBy="coordinate")
     *
     * @var Collection|Cell[]
     */
    protected $cells;

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
