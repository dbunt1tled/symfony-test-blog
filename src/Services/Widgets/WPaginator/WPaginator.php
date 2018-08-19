<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 22.07.18
 * Time: 21:22
 */

namespace App\Services\Widgets\WPaginator;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WPaginator extends \Twig_Extension
{

    /**
     * @var int
     */
    protected $numAnchors = 10;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * WPaginator constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('wPaginator',[$this,'wPaginator'], ['is_safe' => ['html'],'needs_environment' => true])
        ];
    }

    /**
     * @param int $numAnchors
     */
    public function setNumAnchors(int $numAnchors): void
    {
        $this->numAnchors = abs($numAnchors);
    }

    /**
     * @param \Twig_Environment $engine
     * @param Paginator         $posts
     * @param int               $totalItems
     * @param int               $pagesCount
     * @param int               $page
     * @param bool              $large
     * @param string            $aligment
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function wPaginator(\Twig_Environment $engine, Paginator $posts, int $totalItems, int $pagesCount, int $page, bool $large = false, string $aligment = 'left')
    {
        $anchors = intdiv($this->numAnchors,2);
        switch ((string)$aligment) {
            case 'center':
                $alignClass = 'justify-content-center';
                break;
            case 'right':
                $alignClass = 'justify-content-right';
                break;
            default:
                $alignClass = 'justify-content-left';
                break;
        }
        return $engine->render('widgets/w_paginator/w_paginator.html.twig',compact('posts','totalItems','pagesCount','page','anchors','large','alignClass'));
    }

}