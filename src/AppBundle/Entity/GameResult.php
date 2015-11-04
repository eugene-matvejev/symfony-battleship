<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Timestampable;
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
    use Identifiable, Timestampable;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Player", inversedBy="id")
     * @ORM\JoinColumn(name="winner", referencedColumnName="id")
     *
     * @var Player
     */
    private $winner;

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
}