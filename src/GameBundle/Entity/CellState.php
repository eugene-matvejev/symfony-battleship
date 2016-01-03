<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\NameableTrait;

/**
 * Player
 *
 * @ORM\Table(name="cellState")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\CellStateRepository")
 */
class CellState
{
    use IdentifiableTrait, NameableTrait;
}