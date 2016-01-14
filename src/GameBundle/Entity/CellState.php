<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\IdentifiableTrait;
use EM\GameBundle\ORM\NameableInterface;
use EM\GameBundle\ORM\NameableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(repositoryClass="EM\GameBundle\Repository\CellStateRepository")
 * @ORM\Table(name="cellState")
 */
class CellState implements IdentifiableInterface, NameableInterface
{
    use IdentifiableTrait, NameableTrait;
}