<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractFlaggedEntity;
use EM\GameBundle\ORM\NameableInterface;
use EM\GameBundle\ORM\NameableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player extends AbstractFlaggedEntity implements NameableInterface
{
    use NameableTrait;
}
