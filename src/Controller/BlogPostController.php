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

class BlogPostController extends AbstractController
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
     * @Route("/{categorySlug}/{productSlug}", name="product", requirements={"categorySlug"="[A-Za-z0-9\-]+","productSlug"="[A-Za-z0-9\-]+"})
     * @param $categorySlug
     * @param $productSlug
     *
     * @return Response
     */
    public function show($categorySlug, $productSlug)
    {
        $blog_post = $this->blogService->findOnePostBySlug($productSlug);
        $category = $this->blogService->findOneCategoryBySlug($categorySlug);
        if(!$blog_post || !$category || ($blog_post->getCategory() != $category) ) {
            throw new NotFoundHttpException('Something went wrong. Post not Found');
        }
        return $this->render('blog-post/show.html.twig',compact('blog_post','category'));
    }
}