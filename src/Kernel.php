<?php

namespace App;

use App\Utils\Globals;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }
        parent::boot();
        $container = $this->getContainer();
        $imagesRootDirectory = '';
        $imagesUrlDirectory = '';
        if ($container->hasParameter('images_root_directory')) {
            $imagesRootDirectory = $container->getParameter('images_root_directory');
        }
        if ($container->hasParameter('images_url_directory')) {
            $imagesUrlDirectory = $container->getParameter('images_url_directory');
        }

        if ($container->hasParameter('categories_files_directory')) {
            Globals::setCategoryImagesDir($imagesRootDirectory.$container->getParameter('categories_files_directory'));
            Globals::setCategoryImagesUrl($imagesUrlDirectory.$container->getParameter('categories_files_directory'));
        }
        if ($container->hasParameter('blog_posts_files_directory')) {
            Globals::setBlogImagesDir($imagesRootDirectory.$container->getParameter('blog_posts_files_directory'));
            Globals::setBlogImagesUrl($imagesUrlDirectory.$container->getParameter('blog_posts_files_directory'));
        }
        if ($container->hasParameter('users_files_directory')) {
            Globals::setUserImagesDir($imagesRootDirectory.$container->getParameter('users_files_directory'));
            Globals::setUserImagesUrl($imagesUrlDirectory.$container->getParameter('users_files_directory'));
        }
        if ($container->hasParameter('paginator_page_size')) {
            Globals::setPaginatorPageSize($container->getParameter('paginator_page_size'));
        }
    }
}
