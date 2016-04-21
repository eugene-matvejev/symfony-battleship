<?php

namespace EM\GameBundle\ORM;

/**
 * @since 12.1
 */
interface MaskAwareInterface
{
    public function addMask(int $mask);

    public function removeMask(int $mask);

    public function setMask(int $mask);

    public function getMask();

    public function hasMask(int $mask);
}
