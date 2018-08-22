<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 19.08.18
 * Time: 20:45
 */

namespace App\Services\Widgets\WCategory;

use App\Entity\Category;
use App\UseCases\Blog\BlogService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WCategoriesChildren extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var BlogService
     */
    private $blogService;

    public function __construct(ContainerInterface $container, BlogService $blogService)
    {
        $this->container = $container;
        $this->blogService = $blogService;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('WCategoriesChildren',[$this,'WCategoriesChildren'], ['is_safe' => ['html'],'needs_environment' => true])
        ];
    }

    public function WCategoriesChildren(\Twig_Environment $engine, Category $category)
    {
        $categories = $category->getChildren();

        return $engine->render('widgets/categories/w-categories-all/w-categories-children.html.twig',compact('categories'));
    }
}