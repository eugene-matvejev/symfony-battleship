<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="cellState")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CellStateRepository")
 */
class CellState
{
    use Identifiable, Nameable;
}