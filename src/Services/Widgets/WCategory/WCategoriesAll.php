<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 19.08.18
 * Time: 20:45
 */

namespace App\Services\Widgets\WCategory;

use App\UseCases\Blog\BlogService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WCategoriesAll extends \Twig_Extension
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
            new \Twig_SimpleFunction('WCategoriesAll',[$this,'WCategoriesAll'], ['is_safe' => ['html'],'needs_environment' => true])
        ];
    }

    public function WCategoriesAll(\Twig_Environment $engine)
    {
        //$categories = $this->blogService->getAllCategoryTree();
        //$container = $this->container;
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="/'.$node['slug'].'">'.$node['name'].'</a>';
            }
        );
        $htmlTree = $this->blogService->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* false: load all children, true: only direct */
            $options
        );
        return $engine->render('widgets/categories/w-categories-all/w-categories-all.html.twig',compact('htmlTree'));
    }
}