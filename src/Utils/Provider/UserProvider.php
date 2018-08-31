<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 22.08.18
 * Time: 22:16
 */

namespace App\Utils\Provider;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\NoResultException;

class UserProvider implements UserProviderInterface
{
    /**
     * @var ObjectRepository
     */
    private $userRepository;

    /**
     * UserProvider constructor.
     *
     * @param ObjectRepository $userRepository
     */
    public function __construct(ObjectRepository $userRepository){

        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername($username)
    {
        try {
            $user = $this->userRepository->loadUserByUsername($username);
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeDemoBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        } catch (NonUniqueResultException $e) {
            $message = sprintf(
                'Unable to find an active admin AcmeDemoBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }
        return $this->userRepository->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->userRepository->getClassName() === $class
               || is_subclass_of($class, $this->userRepository->getClassName());
    }
}