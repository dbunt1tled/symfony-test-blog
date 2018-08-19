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

class AuthorController extends AbstractController
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
     * @Route("/author/{slug}.html", name="author", requirements={"slug"="[A-Za-z0-9\-]+"})
     * @param $slug
     *
     * @return Response
     */
    public function show($slug, Request $request)
    {
        $page = (int)$request->query->get('page',1);

        $author = $this->blogService->findOneAuthorBySlug($slug);
        if(empty($author)){
            throw new NotFoundHttpException('Something went wrong. Category not found');
        }
        $orderBy = [];
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';
        $postData = $this->blogService->getAllPostByAuthorPaginator($author,true,true,true,true,false,$page,Globals::getPaginatorPageSize(),$orderBy);
        return $this->render('author/show.html.twig', [
            'posts' => $postData['posts'],
            'totalItems' => $postData['totalItems'],
            'page' => $page,
            'author' => $author,
            'pagesCount' => $postData['pagesCount'],
            'title' => $author->getName(),
        ]);
    }
}