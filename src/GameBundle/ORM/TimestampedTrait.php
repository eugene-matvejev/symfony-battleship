<?php

namespace EM\GameBundle\ORM;

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

    public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }

    /**
     * @ORM\PrePersist
     */
    public function setTimestamp()
    {
        $this->timestamp = new \DateTime();
    }
}