<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\PlayerInterface;
use EM\GameBundle\ORM\PlayerTrait;
use EM\GameBundle\ORM\TimestampedInterface;
use EM\GameBundle\ORM\TimestampedTrait;
use JMS\Serializer\Annotation as Serializer;

/**
 * @since 22.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="player_sessions",
 *      indexes={
 *          @ORM\Index(name="INDEX_SESSION_HASH", columns={"hash"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 *
 * @Serializer\ExclusionPolicy("ALL")
 */
class PlayerSession extends AbstractEntity implements PlayerInterface, TimestampedInterface
{
    use PlayerTrait, TimestampedTrait;
    /**
     * @ORM\Column(name="hash", type="string", length=40)
     * @Serializer\Expose()
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected $hash;

    public function getHash() : string
    {
        return $this->hash;
    }

    public function setHash(string $hash) : self
    {
        $this->hash = $hash;

        return $this;
    }
}
