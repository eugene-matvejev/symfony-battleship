<?php

namespace GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GameBundle\Library\ORM\IdentifiableInterface;
use GameBundle\Library\ORM\IdentifiableTrait;
use GameBundle\Library\ORM\NameableInterface;
use GameBundle\Library\ORM\NameableTrait;

/**
 * PlayerType
 *
 * @ORM\Table(name="playerType")
 * @ORM\Entity(repositoryClass="GameBundle\Repository\PlayerTypeRepository")
 */
class PlayerType implements IdentifiableInterface, NameableInterface
{
    use IdentifiableTrait, NameableTrait;
}