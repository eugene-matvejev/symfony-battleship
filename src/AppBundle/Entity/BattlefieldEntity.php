<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Battlefield
 *
 * ORM\Table(name="battlefields")
 * ORM\Entity(repositoryClass="AppBundle\Repository\BattlefieldRepository")
 */
class BattlefieldEntity
{
    use Identifiable;

    /**
     * @var
     */
    private $player;

    /**
     * @var
     */
    private $game;

    /**
     * @var CellEntity[]
     */
    private $cells;

    /**
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param PlayerEntity $player
     * @return $this
     */
    public function setPlayer(PlayerEntity $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param GameEntity $game
     * @return $this
     */
    public function setGame(GameEntity $game)
    {
        $this->game = $game;

        return $this;
    }

    public function addCell(CellEntity $cell) {
        $this->cells[] = $cell;
    }
}