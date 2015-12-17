<?php

namespace AppBundle\Entity;

use AppBundle\Library\Traits\Identifiable;
use AppBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerType
 *
 * @ORM\Table(name="playerType")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerTypeRepository")
 */
class PlayerType
{
    use Identifiable, Nameable;
}