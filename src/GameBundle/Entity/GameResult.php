<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\PlayerInterface;
use EM\GameBundle\ORM\TimestampedInterface;
use EM\GameBundle\ORM\IdentifiableTrait;
use EM\GameBundle\ORM\PlayerTrait;
use EM\GameBundle\ORM\TimestampedTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(repositoryClass="EM\GameBundle\Repository\GameResultRepository", readOnly=true)
 * @ORM\Table(
 *     name="gamesResults",
 *     indexes={
 *          @ORM\Index(name="INDEX_GAME_RESULT_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_GAME_RESULT_WINNER", columns={"player"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class GameResult implements IdentifiableInterface, PlayerInterface, TimestampedInterface
{
    use IdentifiableTrait, PlayerTrait, TimestampedTrait;
    /**
     * @ORM\OneToOne(targetEntity="EM\GameBundle\Entity\Game", inversedBy="result")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @var Game
     */
    private $game;

    public function getGame() : Game
    {
        return $this->game;
    }

    public function setGame(Game $game) : self
    {
        $this->game = $game;

        return $this;
    }
}