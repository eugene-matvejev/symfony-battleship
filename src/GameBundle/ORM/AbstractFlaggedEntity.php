<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @since 13.1
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractFlaggedEntity extends AbstractEntity implements FlaggedInterface
{
    /**
     * @ORM\Column(name="flag", type="integer")
     *
     * @var int
     */
    protected $mask;

    public function addFlag(int $mask) : self
    {
        $this->mask |= $mask;

        return $this;
    }

    public function removeFlag(int $mask) : self
    {
        $this->mask &= ~$mask;

        return $this;
    }

    public function setFlag(int $mask) : self
    {
        $this->mask = $mask;

        return $this;
    }

    public function getFlag() : int
    {
        return $this->mask;
    }

    public function hasFlag(int $mask) : bool
    {
        return ($this->mask & $mask) === $mask;
    }
}
