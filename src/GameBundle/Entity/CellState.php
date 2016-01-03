<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\Interfaces\IdentifiableInterface;
use GameBundle\Library\Traits\Identifiable;
use GameBundle\Library\Traits\Nameable;

/**
 * Player
 *
 * @ORM\Table(name="cellState")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\CellStateRepository")
 */
class CellState implements IdentifiableInterface
{
    use Identifiable, Nameable;
}