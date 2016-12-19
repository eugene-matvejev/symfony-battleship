<?php

namespace EM\FoundationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EM\FoundationBundle\ORM\AbstractFlaggedEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @since 23.0
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name = "users",
 *      indexes = {
 *          @ORM\Index(name="INDEX_USER_EMAIL", columns={"email"}),
 *          @ORM\Index(name="INDEX_USER_EMAIL_AND_PASSWORD", columns={"email", "passwordHash"}),
 *      }
 * )
 *
 * @JMS\AccessorOrder(order="custom", custom={"id", "flag", "email"})
 * @JMS\XmlRoot("user")
 */
class User extends AbstractFlaggedEntity
{
    /**
     * @ORM\Column(name="email", type="string")
     *
     * @JMS\Type("string")
     *
     * @var string
     */
    private $email;
    /**
     * @ORM\Column(name="passwordHash", type="string", length=40)
     *
     * @JMS\Exclude()
     *
     * @var string
     */
    private $passwordHash;

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswordHash() : string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $hash) : self
    {
        $this->passwordHash = $hash;

        return $this;
    }
}
