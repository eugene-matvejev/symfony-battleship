<?php
namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query;

/**
 * Game
 *
 * ORM\Table(name="games")
 * ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class GameEntity
{
    use Identifiable;
}