<?php

namespace GameBundle\Library\ORM;

/**
 * @since 1.0
 */
trait TimestampedTrait
{
    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @ORM\PrePersist
     */
    public function setTimestamp()
    {
        $this->timestamp = new \DateTime();
    }

    public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }
}