<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Battlefield
 *
 * @ORM\Table(name="battlefields")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattlefieldRepository")
 */
class BattlefieldEntity
{
    use Identifiable;

    /**
     * @ORM\ManyToOne(targetEntity="PlayerEntity")
     * @ORM\JoinColumn(name="player", referencedColumnName="id")
     *
     * @var PlayerEntity
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="GameEntity")
     * @ORM\JoinColumn(name="game", referencedColumnName="id")
     *
     * @var GameEntity
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="CellEntity", mappedBy="battlefield")
     * @ORM\JoinColumn(name="id", referencedColumnName="battlefield")
     *
     * @var CellEntity[]
     */
    private $cells;

    /**
     * @return PlayerEntity
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param PlayerEntity $player
     *
     * @return $this
     */
    public function setPlayer(PlayerEntity $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return GameEntity
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param GameEntity $game
     *
     * @return $this
     */
    public function setGame(GameEntity $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return CellEntity[]
     */
    public function getCells()
    {
        return $this->cells;
    }
}