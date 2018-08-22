<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.08.18
 * Time: 13:37
 */

namespace App\Services\Routing;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class BlogRouting extends Loader
{
    private $isLoaded = false;
    //private $em;

    public function __construct(/*EntityManager $entityManager/**/) {
        //$this->em = $entityManager;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();
        /*
        // prepare a new route
        $path = '/{page}';
        $defaults = [
            '_controller' => 'App\Controller\HomeController::index',
            'page' => '1'
        ];
        $requirements = [
            'page' => '\d+',
            'defaults' => '1'
        ];
        $route = new Route($path, $defaults, $requirements);

        // add the new route to the route collection
        $routeName = 'home';
        $routes->add($routeName, $route);

        $this->isLoaded = true;
        /**/
        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'blog_route' === $type;
    }

}