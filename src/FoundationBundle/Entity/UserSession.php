<?php

namespace EM\FoundationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\ORM\AbstractEntity;
use EM\FoundationBundle\ORM\TimestampedInterface;
use EM\FoundationBundle\ORM\TimestampedTrait;
use EM\FoundationBundle\ORM\UserAwareInterface;
use EM\FoundationBundle\ORM\UserAwareTrait;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 23.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name = "user_sessions",
 *      indexes = {
 *          @ORM\Index(name="INDEX_SESSION_HASH", columns={"hash"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class UserSession extends AbstractEntity implements UserAwareInterface, TimestampedInterface
{
    use UserAwareTrait, TimestampedTrait;
    /**
     * @ORM\Column(name="hash", type="string", length=40)
     *
     * @JMS\Expose()
     * @JMS\Type("string")
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
