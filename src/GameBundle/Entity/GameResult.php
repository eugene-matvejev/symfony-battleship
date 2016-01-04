<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\ORM\IdentifiableInterface;
use GameBundle\Library\ORM\PlayerInterface;
use GameBundle\Library\ORM\TimestampedInterface;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\PlayerTrait;
use GameBundle\Library\ORM\TimestampedTrait;

/**
 * GameResult
 *
 * @ORM\Table(
 *     name="gamesResults",
 *     indexes={
 *          @ORM\Index(name="INDEX_GAME_RESULT_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_GAME_RESULT_WINNER", columns={"player"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="GameBundle\Repository\GameResultRepository")
 */
class GameResult implements IdentifiableInterface, PlayerInterface, TimestampedInterface
{
    use IdentifiableTrait, PlayerTrait, TimestampedTrait;
    /**
     * @ORM\OneToOne(targetEntity="GameBundle\Entity\Game", mappedBy="result")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    private $game;

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
}