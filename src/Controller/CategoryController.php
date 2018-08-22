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

class CategoryController extends AbstractController
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
     * @Route("/{slug}/{page}", name="category", defaults={"page"="1"}, requirements={"slug"="[A-Za-z0-9\-]+","page"="[0-9\-]+"})
     * @param $slug
     * @param $page
     *
     * @return Response
     */
    public function show($slug,$page)
    {
        $category = $this->blogService->findOneCategoryBySlug($slug);
        if(empty($category)){
            throw new NotFoundHttpException('Something went wrong. Category not found');
        }
        $orderBy = [];
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';
        $postData = $this->blogService->getAllPostByCategoryPaginator($category,true,true,true,true,false,$page,Globals::getPaginatorPageSize(),$orderBy);
        return $this->render('category/show.html.twig', [
            'posts' => $postData['posts'],
            'totalItems' => $postData['totalItems'],
            'page' => $page,
            'category' => $category,
            'pagesCount' => $postData['pagesCount'],
            'title' => $category->getName(),
        ]);
    }
}