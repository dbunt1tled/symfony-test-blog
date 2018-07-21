<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 20.07.18
 * Time: 15:52
 */

namespace App\Services\Auth\Login;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class LoginSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    private $flashBag;
    public function __construct(HttpUtils $httpUtils, array $options = [], FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        parent::__construct($httpUtils, $options);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $this->flashBag->add('success','Congratulation you successfully login on Our Site');
        return parent::onAuthenticationSuccess($request, $token);
    }
}