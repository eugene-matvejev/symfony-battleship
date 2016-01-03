<?php

namespace GameBundle\Entity;

use GameBundle\Library\Interfaces\IdentifiableInterface;
use GameBundle\Library\Interfaces\PlayerInterface;
use GameBundle\Library\Interfaces\TimestampedInterface;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\PlayerTrait;
use GameBundle\Library\ORM\TimestampedTrait;
use GameBundle\Library\Traits\Identifiable;
use GameBundle\Library\Traits\Timestamped;
use Doctrine\ORM\Mapping as ORM;

/**
 * GameResult
 *
 * @ORM\Table(
 *     name="gamesResults",
 *     indexes={
 *          @ORM\Index(name="INDEX_GAME_RESULT_GAME", columns={"game"}),
 *          @ORM\Index(name="INDEX_GAME_RESULT_WINNER", columns={"winner"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="GameBundle\Repository\GameResultRepository")
 */
class GameResult implements IdentifiableInterface, PlayerInterface, TimestampedInterface
{
    use IdentifiableTrait, PlayerTrait, TimestampedTrait;

}