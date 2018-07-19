<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\Auth\LoginType;
use App\Form\Auth\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $error = $this->authenticationUtils->getLastAuthenticationError();
        if(!empty($error)){
            $this->addFlash('danger', 'Error login: ');
        }
        dump($error);
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
        $user = new Author();
        $form = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Registration is successfully ');
            return $this->redirectToRoute('home');
        }
        return $this->render('auth/register.html.twig', [
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \RuntimeException('This should never be called directly');
    }
}
