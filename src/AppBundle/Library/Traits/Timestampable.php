<?php

namespace AppBundle\Library\Traits;

trait Timestampable {
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

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}