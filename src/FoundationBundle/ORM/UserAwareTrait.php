<?php

namespace EM\FoundationBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\Entity\User;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 2.0
 */
trait UserAwareTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="EM\FoundationBundle\Entity\User", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="player", referencedColumnName="id", nullable=false)
     *
     * @JMS\Type("EM\FoundationBundle\Entity\User")
     *
     * @var User
     */
    protected $user;

    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return static
     */
    public function setUser(User $user) : self
    {
        $this->user = $user;

        return $this;
    }
}
