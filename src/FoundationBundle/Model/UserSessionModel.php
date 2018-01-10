<?php

namespace EM\FoundationBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;
use EM\FoundationBundle\Entity\User;
use EM\FoundationBundle\Entity\UserSession;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

/**
 * @since 23.0
 */
class UserSessionModel
{
    const TTL            = 60 * 60 * 24 * 30;
    const SESSION_HEADER = 'x-wsse';
    /**
     * @var ObjectRepository
     */
    private $sessionRepository;
    /**
     * @var ObjectRepository
     */
    private $playerRepository;
    /**
     * @var UserModel
     */
    private $model;
    /**
     * @var string
     */
    private $salt;

    public function __construct(ObjectRepository $sessionRepository, ObjectRepository $playerRepository, UserModel $model, string $salt)
    {
        $this->sessionRepository = $sessionRepository;
        $this->playerRepository  = $playerRepository;
        $this->model             = $model;
        $this->salt              = $salt;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return UserSession
     * @throws BadCredentialsException
     */
    public function authenticate(string $email, string $password) : UserSession
    {
        $passwordHash = $this->model->generatePasswordHash($email, $password);

        /** @var User $player */
        $player = $this->playerRepository->findOneBy(['email' => $email, 'passwordHash' => $passwordHash]);

        if (!$player) {
            throw new BadCredentialsException();
        }

        return $this->create($player);
    }

    /**
     * @param string $hash
     *
     * @return UserSession
     * @throws CredentialsExpiredException
     * @throws BadCredentialsException
     */
    public function find(string $hash) : UserSession
    {
        /** @var UserSession $session */
        if (null !== $session = $this->sessionRepository->findOneBy(['hash' => $hash])) {
            if ($session->getTimestamp()->getTimestamp() + static::TTL >= time()) {
                return $session;
            }

            throw new CredentialsExpiredException();
        }

        throw new BadCredentialsException();
    }

    private function create(User $player) : UserSession
    {
        $session = new UserSession();
        $session
            ->setUser($player)
            ->setHash($this->generateSessionHash($player));

        return $session;
    }

    private function generateSessionHash(User $player) : string
    {
        return sha1("{$player->getEmail()}:{$player->getPasswordHash()}:{$this->salt}:" . microtime(true));
    }
}
