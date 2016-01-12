<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\ORM\IdentifiableInterface;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\NameableInterface;
use GameBundle\Library\ORM\NameableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(repositoryClass="GameBundle\Repository\CellStateRepository")
 * @ORM\Table(name="cellState")
 */
class CellState implements IdentifiableInterface, NameableInterface
{
    use IdentifiableTrait, NameableTrait;
}