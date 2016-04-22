<?php

namespace EM\GameBundle\ORM;

/**
 * @since 13.1
 */
interface FlaggedInterface
{
    public function addFlag(int $mask);

    public function removeFlag(int $mask);

    public function setFlags(int $mask);

    public function getFlags();

    public function hasFlag(int $mask);
}
