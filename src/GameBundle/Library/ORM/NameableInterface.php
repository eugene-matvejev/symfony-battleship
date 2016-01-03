<?php

namespace GameBundle\Library\Interfaces;

/**
 * @since 3.1
 */
interface NameableInterface
{
    public function getName();

    public function setName($name);
}