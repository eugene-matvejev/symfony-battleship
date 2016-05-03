<?php

namespace EM\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\GameBundle\ORM\AbstractFlaggedEntity;

/**
 * @since 1.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="players",
 *      indexes={
 *          @ORM\Index(name="INDEX_PLAYER_NAME", columns={"name"})
 *      }
 * )
 */
class Player extends AbstractFlaggedEntity
{
    /**
     * @ORM\Column(name="name", type="string", length=100)
     *
     * @var string
     */
    protected $name;

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }
}
