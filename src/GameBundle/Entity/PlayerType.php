<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractEntity;

/**
 * @since 1.0
 *
 * @ORM\Entity(repositoryClass="EM\GameBundle\Repository\PlayerTypeRepository", readOnly=true)
 * @ORM\Table(name="player_types")
 */
class PlayerType extends AbstractEntity
{
}
