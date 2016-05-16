<?php

namespace EM\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\PlayerInterface;
use EM\GameBundle\ORM\PlayerTrait;
use JMS\Serializer\Annotation as JMS;

/**
 * EM\GameBundle\Entity\Battlefield:
 * accessor_order: custom
 * custom_accessor_order: []
 *
 * xml_root_name: battlefield
 *
 * properties:
 * id:
 * type: integer
 * player:
 * type: EM\GameBundle\Entity\Player
 * serialized_name: player
 * cells:
 * type: array<EM\GameBundle\Entity\Cell>
 * serialized_name: cells
 * xml_list:
 * inline: false
 * entry_name: cell
 */

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
 *
 * @JMS\AccessorOrder(order="custom", custom={"id", "player", "cells"})
 * @JMS\XmlRoot("battlefield")
 */
class Battlefield extends AbstractEntity implements PlayerInterface
{
    use PlayerTrait;
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
     * @JMS\Type("EM\GameBundle\Entity\Cell")
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
