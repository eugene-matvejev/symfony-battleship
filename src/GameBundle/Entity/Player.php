<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;
use EM\GameBundle\ORM\AbstractMaskAwareEntity;
use EM\GameBundle\ORM\NameableInterface;
use EM\GameBundle\ORM\NameableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player extends AbstractMaskAwareEntity implements NameableInterface
{
    use NameableTrait;
}
