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
     * @ORM\Column(name="flags", type="integer")
     *
     * @var int
     */
    protected $flag;

    public function addFlag(int $flag) : self
    {
        $this->flag |= $flag;

        return $this;
    }

    public function removeFlag(int $flag) : self
    {
        $this->flag &= ~$flag;

        return $this;
    }

    public function setFlags(int $flag) : self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getFlags() : int
    {
        return $this->flag;
    }

    public function hasFlag(int $flag) : bool
    {
        return ($this->flag & $flag) === $flag;
    }
}
