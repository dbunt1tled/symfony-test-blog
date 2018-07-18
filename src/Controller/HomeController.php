<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Main Page',
        ]);
    }

    /**
     * @Route("/about", name="about")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function about(Request $request)
    {
        $name = $request->get('name','User');
        return $this->render('home/about.html.twig', [
            'title' => 'About',
            'name' => $name,
        ]);
    }

    /**
     * @Route("/ask/{slug}", name="ask", defaults={"slug"="Shirley Manson"}, requirements={"slug"="[A-Za-z0-9\-]+"})
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ask($slug)
    {
        $name = ucwords(str_replace('-', ' ', $slug));
        return $this->render('home/ask.html.twig', [
            'title' => 'Ask',
            'slug' => $slug,
            'name' => $name,
        ]);
    }
}
