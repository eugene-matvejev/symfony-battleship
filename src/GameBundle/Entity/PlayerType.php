<?php

namespace GameBundle\Entity;

use GameBundle\Library\Traits\Identifiable;
use GameBundle\Library\Traits\Nameable;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerType
 *
 * @ORM\Table(name="playerType")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\PlayerTypeRepository")
 */
class PlayerType
{
    use Identifiable, Nameable;
}