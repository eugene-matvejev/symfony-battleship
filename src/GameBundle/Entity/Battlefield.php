<?php

namespace EM\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\ORM\AbstractEntity;
use EM\FoundationBundle\ORM\UserAwareInterface;
use EM\FoundationBundle\ORM\UserAwareTrait;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 1.0
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="battlefields",
 *     indexes={
 *          @ORM\Index(name="INDEX_BATTLEFIELDS_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_BATTLEFIELDS_PLAYER", columns={"player"})
 *     }
 * )
 *
 * @JMS\AccessorOrder(order="custom", custom={"id", "player", "cells"})
 * @JMS\XmlRoot("battlefield")
 */
class Battlefield extends AbstractEntity implements UserAwareInterface
{
    use UserAwareTrait;
    /**
     * @ORM\ManyToOne(targetEntity="EM\GameBundle\Entity\Game", inversedBy="battlefields", fetch="EAGER")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @JMS\Exclude()
     *
     * @var Game
     */
    protected $game;
    /**
     * @ORM\OneToMany(targetEntity="EM\GameBundle\Entity\Cell", mappedBy="battlefield", cascade={"persist"}, fetch="EAGER", indexBy="coordinate")
     *
     * @JMS\Type("array<EM\GameBundle\Entity\Cell>")
     * @JMS\XmlList(entry="cell")
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
        $this->cells->set($cell->getCoordinate(), $cell);
        $cell->setBattlefield($this);

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
