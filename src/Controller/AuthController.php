<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Auth\LoginType;
use App\Form\Auth\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends Controller
{
    private $authenticationUtils;
    private $passwordEncoder;

    public function __construct(AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     * @return Response
     */
    public function login(): Response
    {

        // get the login error if there is one
        $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginType::class,['username' =>$lastUsername]);
        return $this->render('auth/login.html.twig', [
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createRegistrationForm($user);
        return $this->render('auth/register.html.twig', [
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/register-form-handle", name="register_form_handle", methods={"POST"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function handleRegisterForm(Request $request)
    {
        $user = new User();
        $form = $this->createRegistrationForm($user);
        $form->handleRequest($request);
        if(!$form->isSubmitted() || !$form->isValid())
        {
            return $this->render('auth/register.html.twig', [
                'form'          => $form->createView(),
            ]);
        }
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        //main - this firewall in security.yamal
        $token = new UsernamePasswordToken($user,$password,'main',$user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main',serialize($token));

        $this->addFlash('success', 'Registration is successfully ');
        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \RuntimeException('This should never be called directly');
    }

    /**
     * @param $user
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createRegistrationForm($user): \Symfony\Component\Form\FormInterface
    {
        return $this->createForm(RegisterType::class, $user, [
            'action' => $this->generateUrl('register_form_handle')
        ]);

}
}
