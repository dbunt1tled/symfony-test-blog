<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 24.08.18
 * Time: 15:37
 */

namespace App\Listeners\Subscriber;

use App\Controller\Rest\Midelware\AuthenticatedController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthSubscriber /*implements EventSubscriberInterface/**/
{
    private  $user;
    private  $restrict;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage,AuthorizationCheckerInterface $authorizationChecker,int $restrict = 0)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->restrict = $restrict;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof AuthenticatedController) {
            if (false === $this->authorizationChecker->isGranted('ROLE_MODERATOR')) {
                throw new AccessDeniedHttpException('This action needs a valid token!');
            }
        }
    }

    /**
     * @return mixed
     */
    public function getRestrict()
    {
        return $this->restrict;
    }

    /**
     * @param mixed $restrict
     */
    public function setRestrict($restrict): void
    {
        $this->restrict = $restrict;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
    public static function getSubscribedEvents()
    {
        /*return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );/**/
    }
}