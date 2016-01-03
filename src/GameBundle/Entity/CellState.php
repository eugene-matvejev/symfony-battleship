<?php
namespace GameBundle\Entity;

use GameBundle\Library\Traits\Identifiable;
use GameBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="cellState")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\CellStateRepository")
 */
class CellState
{
    use Identifiable, Nameable;
}