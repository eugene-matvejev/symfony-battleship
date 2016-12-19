<?php

namespace EM\FoundationBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @see   AbstractFlaggedEntityTest
 *
 * @since 13.1
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractFlaggedEntity extends AbstractEntity implements FlaggedInterface
{
    /**
     * @ORM\Column(name="flags", type="integer")
     *
     * @JMS\Type("integer")
     *
     * @var int
     */
    protected $flags;

    /**
     * @param int $flag
     *
     * @return static
     */
    public function addFlag(int $flag) : self
    {
        $this->flags |= $flag;

        return $this;
    }

    /**
     * @param int $flag
     *
     * @return static
     */
    public function removeFlag(int $flag) : self
    {
        $this->flags &= ~$flag;

        return $this;
    }

    public function getFlags() : int
    {
        return $this->flags;
    }

    /**
     * @param int $flag
     *
     * @return static
     */
    public function setFlags(int $flag) : self
    {
        $this->flags = $flag;

        return $this;
    }

    public function hasFlag(int $flag) : bool
    {
        return ($this->flags & $flag) === $flag;
    }
}
