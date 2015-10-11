<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Player
 *
 * @ORM\Table(name="players")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class PlayerEntity
{
    use Identifiable, Nameable;
}