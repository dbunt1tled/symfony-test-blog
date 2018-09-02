<?php

namespace App\Controller\Rest\v1;


use App\Controller\Rest\v1\Resource\CategoryDetailResource;
use App\Entity\Category;
use App\UseCases\Blog\BlogService;
use App\Utils\Globals;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class IndexController extends FOSRestController
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
     * @Rest\Get("/index/{page}")
     * @return View
     */
    public function index($page = 1): View
    {
        $orderBy['createdAt'] = 'ASC';
        $orderBy['id'] = 'ASC';

        /*$postData = $this->blogService->findAllPosts(true,true,true,true,true,$page,Globals::getPaginatorPageSize());/**/
        $postData = $this->blogService->findOneCategoryBySlug('dr-lavinia-braun');
        dump($postData);
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        $postData = $serializer->serialize($postData, 'json');/**/
        dump($postData);
        die("\n");/**/
        return View::create($postData[0],Response::HTTP_OK)->setFormat('json');
    }

    /**
     * @Rest\Get("/version")
     * @return View
     */
    public function version(): View
    {
        $composerData = JSON::decode(\file_get_contents(__DIR__ . '/../../composer.json'));
        $data = [
            'version' => $composerData->version,
        ];
        return View::create($data,Response::HTTP_OK)->setFormat('json');
    }
}
