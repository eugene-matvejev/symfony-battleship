<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\Interfaces\IdentifiableInterface;
use GameBundle\Library\Traits\Identifiable;
use GameBundle\Library\Traits\Nameable;

/**
 * PlayerType
 *
 * @ORM\Table(name="playerType")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\PlayerTypeRepository")
 */
class PlayerType implements IdentifiableInterface
{
    use Identifiable, Nameable;
}