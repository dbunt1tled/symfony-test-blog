<?php

namespace App\Controller;

use App\Services\Routing\BlogProvider;
use App\UseCases\Blog\BlogService;
use App\Utils\Globals;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @var BlogService
     */
    private $blogService;
    /**
     * @var BlogProvider
     */
    private $blogProvider;

    public function __construct(BlogProvider $blogProvider,  BlogService $blogService)
    {

        $this->blogService = $blogService;
        $this->blogProvider = $blogProvider;
    }

    /*
     * @Route("/{page}", name="home", defaults={"page"="1"}, requirements={"page"="[A-Za-z0-9\-]+"})
     * @param $page
     * @param $request
     *
     * @return Response
     *
    public function home($page, Request $request): Response
    {
        $controller = $this->blogProvider->getController($page);
        return $this->forward($controller, [
            'page' => $page,
            'request' =>  $request,
        ]);
    }
    /**/
    /**
     * @Route("/{page}", name="home", defaults={"page"="1"}, requirements={"page"="[0-9\-]+"})
     * @param $page
     *
     * @return Response
     */
    public function index($page): Response
    {
        $orderBy = [];
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';
        $postData = $this->blogService->getAllPostPaginator(true,true,true,true,true,false,$page,Globals::getPaginatorPageSize(),$orderBy);
        return $this->render('home/index.html.twig', [
            'posts' => $postData['posts'],
            'totalItems' => $postData['totalItems'],
            'page' => $page,
            'pagesCount' => $postData['pagesCount'],
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
