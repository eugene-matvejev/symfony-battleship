<?php

namespace EM\GameBundle\ORM;

/**
 * @since 3.1
 */
interface IdentifiableInterface
{
    public function getId();

    public function setId(int $id);
}
