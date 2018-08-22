<?php

namespace App\Controller\Rest\v1;

use App\UseCases\Blog\BlogService;
use App\Utils\Globals;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class HomeController extends FOSRestController
{
    /**
     * @var BlogService
     */
    private $blogService;

    public function __construct(BlogService $blogService)
    {

        $this->blogService = $blogService;
    }

    /**
     * @Rest\Get("/index/{page}")
     * @return View
     */
    public function index($page = 1): View
    {
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';

        /*$postData = $this->blogService->findAllPosts(true,true,true,true,$page,Globals::getPaginatorPageSize());/**/
        $postData = $this->blogService->findOneCategoryBySlug('dr-lavinia-braun');
        dump($postData);
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $postData = $serializer->serialize($postData, 'json');/**/
        dump($postData);
        die("\n");/**/
        return View::create($postData[0],Response::HTTP_OK)->setFormat('json');
    }

    /**
     * @Rest\Get("/version")
     * @return View
     */
    public function version(): View
    {

        return View::create(['TEST'],Response::HTTP_OK)->setFormat('json');

        return $this->handleView($a);
        dump($a);
        die("\n");
        return 1;

        /*$orderBy = [];
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';
        $postData = $this->blogService->getAllPostPaginator(true,true,true,true,false,$page,Globals::getPaginatorPageSize(),$orderBy);
        return $this->render('home/index.html.twig', [
            'posts' => $postData['posts'],
            'totalItems' => $postData['totalItems'],
            'page' => $page,
            'pagesCount' => $postData['pagesCount'],
            'title' => 'Main Page',
        ]);/**/
    }
}
