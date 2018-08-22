<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.08.18
 * Time: 22:50
 */

namespace App\Controller;

use App\UseCases\Blog\BlogService;
use App\Utils\Globals;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
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
     * @Route("/user/{slug}.html", name="user", requirements={"slug"="[A-Za-z0-9\-]+"})
     * @param $slug
     *
     * @return Response
     */
    public function show($slug, Request $request)
    {
        $page = (int)$request->query->get('page',1);

        $user = $this->blogService->findOneUserBySlug($slug);
        if(empty($user)){
            throw new NotFoundHttpException('Something went wrong. User not found');
        }
        $orderBy = [];
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';
        $postData = $this->blogService->getAllPostByUserPaginator($user,true,true,true,true,false,$page,Globals::getPaginatorPageSize(),$orderBy);
        return $this->render('user/show.html.twig', [
            'posts' => $postData['posts'],
            'totalItems' => $postData['totalItems'],
            'page' => $page,
            'user' => $user,
            'pagesCount' => $postData['pagesCount'],
            'title' => $user->getName(),
        ]);
    }
}