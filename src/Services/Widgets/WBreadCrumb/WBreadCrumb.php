<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 19.08.18
 * Time: 20:45
 */

namespace App\Services\Widgets\WBreadCrumb;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class WBreadCrumb extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var string
     */
    private $homeRoute;
    /**
     * @var string
     */
    private $homeLabel;

    public function __construct(ContainerInterface $container, Breadcrumbs $breadcrumbs, Router $router, $homeRoute = 'homepage', $homeLabel= 'Home')
    {
        $this->container = $container;
        $this->breadcrumbs = $breadcrumbs;
        $this->router = $router;
        $this->homeRoute = $homeRoute;
        $this->homeLabel = $homeLabel;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('WBreadCrumb',[$this,'addBreadcrumb'])
        ];
    }

    public function addBreadcrumb($label, $url = '', array $translationParameters = [])
    {
        if (!$this->breadcrumbs->count()) {
            $this->breadcrumbs->addItem($this->homeLabel, $this->router->generate($this->homeRoute));
        }
        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }

}