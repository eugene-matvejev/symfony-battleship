<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\PlayerInterface;
use EM\GameBundle\ORM\PlayerTrait;
use EM\GameBundle\ORM\TimestampedInterface;
use EM\GameBundle\ORM\TimestampedTrait;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 1.0
 *
 * @ORM\Entity(repositoryClass="EM\GameBundle\Repository\GameResultRepository", readOnly=true)
 * @ORM\Table(
 *     name="game_results",
 *     indexes={
 *          @ORM\Index(name="INDEX_GAME_RESULT_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_GAME_RESULT_WINNER", columns={"player"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\AccessorOrder(order="custom", custom={"id", "timestamp", "player"})
 * @JMS\XmlRoot("game-result")
 */
class GameResult extends AbstractEntity implements PlayerInterface, TimestampedInterface
{
    use PlayerTrait, TimestampedTrait;
    /**
     * @ORM\OneToOne(targetEntity="EM\GameBundle\Entity\Game", inversedBy="result")
     * @ORM\JoinColumn(name="game", referencedColumnName="id", nullable=false)
     *
     * @JMS\Exclude()
     *
     * @var Game
     */
    protected $game;

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
