<?php

namespace Donato\OIDCBundle\Security;

use PROCERGS\VPR\CoreBundle\Entity\User;
use PROCERGS\VPR\CoreBundle\Entity\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OIDCUserProvider implements UserProviderInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var UserRepository */
    private $repository;

    public function __construct(TokenStorageInterface $tokenStorage, UserRepository $repository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->repository = $repository;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'PROCERGS\\VPR\\CoreBundle\\Entity\\User';
    }

    /**
     * @param string $username
     * @return null|object
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $user = $this->repository->findOneBy($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }
}
