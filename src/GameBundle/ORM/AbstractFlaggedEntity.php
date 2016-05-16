<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Type("integer")
     *
     * @var int
     */
    protected $flags;

    public function addFlag(int $flag) : self
    {
        $this->flags |= $flag;

        return $this;
    }

    public function removeFlag(int $flag) : self
    {
        $this->flags &= ~$flag;

        return $this;
    }

    public function setFlags(int $flag) : self
    {
        $this->flags = $flag;

        return $this;
    }

    public function getFlags() : int
    {
        return $this->flags;
    }

    public function hasFlag(int $flag) : bool
    {
        return ($this->flags & $flag) === $flag;
    }
}
