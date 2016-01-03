<?php

namespace GameBundle\Library\Interfaces;

interface TimestampedInterface
{
    public function setTimestamp();

    public function getTimestamp() : \DateTime;
}