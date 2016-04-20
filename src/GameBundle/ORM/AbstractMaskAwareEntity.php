<?php

namespace EM\GameBundle\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * @since 12.1
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractMaskAwareEntity extends AbstractEntity implements MaskAwareInterface
{
    /**
     * @ORM\Column(name="mask", type="integer")
     *
     * @var int
     */
    protected $mask;

    public function addMask(int $mask) : self
    {
        $this->mask |= $mask;

        return $this;
    }

    public function removeMask(int $mask) : self
    {
        $this->mask &= ~$mask;

        return $this;
    }

    public function setMask(int $mask) : self
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask() : int
    {
        return $this->mask;
    }

    public function hasMask(int $mask) : bool
    {
        $asd = ($this->mask & $mask) === $mask;

        return $asd;
    }
}
