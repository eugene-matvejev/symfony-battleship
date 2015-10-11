<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Player
 *
 * ORM\Table(name="cellstate")
 * ORM\Entity(repositoryClass="AppBundle\Repository\CellStateRepository")
 */
class CellStateEntity
{
    use Identifiable, Nameable;
}