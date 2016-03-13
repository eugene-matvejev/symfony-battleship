<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\IdentifiableInterface;
use EM\GameBundle\ORM\IdentifiableTrait;

/**
 * @since 1.0
 *
 * @ORM\Entity(repositoryClass="EM\GameBundle\Repository\CellStateRepository", readOnly=true)
 * @ORM\Table(name="cell_states")
 */
class CellState implements IdentifiableInterface
{
    use IdentifiableTrait;
}
