<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @since 1.0
 */
trait TimestampedTrait
{
    /**
     * @ORM\Column(name="timestamp", type="datetime")
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
     */
    public function setTimestamp() : self
    {
        $this->timestamp = new \DateTime();

        return $this;
    }
}
