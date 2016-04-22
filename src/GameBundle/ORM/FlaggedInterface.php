<?php

namespace EM\GameBundle\ORM;

/**
 * @since 13.1
 */
interface FlaggedInterface
{
    public function addFlag(int $mask);

    public function removeFlag(int $mask);

    public function setFlag(int $mask);

    public function getFlag();

    public function hasFlag(int $mask);
}
