<?php

namespace EM\FoundationBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @see   TimestampedTraitTest
 *
 * @since 1.0
 */
trait TimestampedTrait
{
    /**
     * @ORM\Column(name="timestamp", type="datetime")
     *
     * @JMS\Type("DateTime")
     *
     * @var \DateTime
     */
    protected $timestamp;

    public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @ORM\PrePersist
     *
     * @return static
     */
    public function setTimestamp() : self
    {
        $this->timestamp = new \DateTime();

        return $this;
    }
}
