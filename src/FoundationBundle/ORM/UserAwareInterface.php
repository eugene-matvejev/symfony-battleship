<?php

namespace EM\FoundationBundle\ORM;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\Entity\User;

/**
 * @since 3.1
 */
interface UserAwareInterface
{
    public function getUser() : User;

    public function setUser(User $user);
}
