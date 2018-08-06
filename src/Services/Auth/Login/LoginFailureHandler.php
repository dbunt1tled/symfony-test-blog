<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 20.07.18
 * Time: 15:52
 */

namespace App\Services\Auth\Login;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;

class LoginFailureHandler extends DefaultAuthenticationFailureHandler
{
    private $flashBag;

    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options = [], LoggerInterface $logger = null, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        parent::__construct($httpKernel, $httpUtils, $options, $logger);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->flashBag->add('danger','Oops! Something wrong.');
        return parent::onAuthenticationFailure($request, $exception);
    }
}