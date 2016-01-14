<?php

namespace EM\GameBundle\ORM;

/**
 * @since 3.1
 */
interface NameableInterface
{
    public function getName();

    public function setName($name);
}