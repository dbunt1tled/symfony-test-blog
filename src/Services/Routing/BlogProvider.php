<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.08.18
 * Time: 22:21
 */

namespace App\Services\Routing;

use App\UseCases\Blog\BlogService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogProvider
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
     * @param $parameter
     *
     * @return string
     */
    public function getController($parameter)
    {
        if(is_numeric($parameter)){
            return 'App\Controller\HomeController::index';
        }
        $post = $this->blogService->findOnePostBySlug($parameter);
        if($post) {
            return 'App\Controller\BlogPostController::show';
        }
        $category = $this->blogService->findOneCategoryBySlug($parameter);
        if($category) {
            return 'App\Controller\CategoryController::show';
        }
        throw new NotFoundHttpException('Something went wrong!!! Page not found.');
    }
}