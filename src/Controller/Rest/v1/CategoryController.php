<?php

namespace App\Controller\Rest\v1;

use App\Controller\Rest\Midelware\AuthenticatedController;
use App\Controller\Rest\v1\Resource\CategoryDetailResource;
use App\Entity\Category;
use App\Services\Security\Voters\ManageCategoryVoter;
use App\UseCases\Blog\BlogService;
use App\UseCases\Images\ImageUploader;
use App\Utils\Globals;
use App\ValuesObject\Category\CategoryCreateVO;
use App\ValuesObject\Category\CategoryEditVO;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CategoryController extends FOSRestController implements AuthenticatedController
{
    /**
     * @var BlogService
     */
    private $blogService;
    /**
     * @var ImageUploader
     */
    private $uploader;
    
    public function __construct(ImageUploader $uploader, BlogService $blogService)
    {

        $this->blogService = $blogService;
        $this->uploader = $uploader;
    }
    
    /**
     * @Rest\Get("/category", name="api_category_list")
     * @param Request $request
     *
     * @return View
     */
    public function list(Request $request)
    {
        header('Content-Type: cli');
        $page = $request->get('page',1);
        $perPage = $request->get('perPage',Globals::getPaginatorPageSize());
        $categories = $this->blogService->getAllCategoryPaginator(true,true,true,$page,$perPage);
        $mapCategory = new CategoryDetailResource();
        
        //$result = iterator_to_array($categories['categories']);
        /*header('Content-Type: cli');
        dump($result);
        die;/**/
        
        $result = [
            'success'=>true,
            '_metadata' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalItems' => $categories['totalItems'],
                'pagesCount' => $categories['pagesCount'],
            ],
            'categories' => $mapCategory->listArray($categories['categories']),
        ];
        return View::create($result,Response::HTTP_OK)->setFormat('json');
    }
    /**
     * @Rest\Get("/category/{category_id}", name="api_category_show")
     * @ParamConverter("category", class="App\Entity\Category", options={"mapping": {"category_id" : "id"}})
     * @param Category                       $category
     *
     * @return View
     */
    public function show(Category $category = null)
    {
        if (empty($category)) {
            return View::create(['success'=>false, 'message' => 'Category not found'],Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        $mapCategory = new CategoryDetailResource();
        $result = [
            'success'=>true,
            '_metadata' => [
            ],
            'category' => $mapCategory->toArray($category),
        ];
        return View::create($result,Response::HTTP_OK)->setFormat('json');
    }
    
    /**
     * @Rest\Post("/category", name="api_category_create")
     * @ParamConverter("categoryVO", converter="fos_rest.request_body")
     * @param CategoryCreateVO                       $categoryVO
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(CategoryCreateVO $categoryVO, ConstraintViolationListInterface $validationErrors): View
    {
        if (\count($validationErrors) > 0) {
            return View::create($validationErrors,Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        $category = $this->blogService->makeCategoryByVO($categoryVO);
        $this->denyAccessUnlessGranted(ManageCategoryVoter::ADD, $category);
        $this->blogService->saveCategory($category);
        
        $categoryUrl = $this->generateUrl(
            'api_category_show',
            ['id' => $category->getId()]
        );
        return View::createRedirect($categoryUrl);
    }
    
    /**
     * @Rest\Put("/category/{category_id}/image", name="api_category_update_image")
     * @ParamConverter("category", class="App\Entity\Category", options={"mapping": {"category_id" : "id"}})
     * @param Category|null $category
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateImage(Category $category = null): View
    {
        if (empty($category)) {
            return View::create(['success'=>false, 'message' => 'Category not found'],Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        $this->denyAccessUnlessGranted(ManageCategoryVoter::EDIT, $category);
        try{
            $file = tmpfile();
            if ($file === false) {
                throw new \Exception('File can not be opened.');
            }
            $path = stream_get_meta_data($file)['uri'];
            $uploadedFile = $this->uploader->getPutFile($path,$category->getSlug());
        }catch (\Exception $ex) {
            return View::create(['success'=>false, 'message' => $ex->getMessage()],Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        $category->setImage($uploadedFile);
        $this->uploader->uploadFile($category);
        $this->blogService->saveCategory($category);
        
        $categoryUrl = $this->generateUrl(
            'api_category_show',
            ['id' => $category->getId()]
        );
        return View::createRedirect($categoryUrl);
    }
    /**
     * @Rest\Patch("/category/{category_id}", name="api_category_update")
     * @ParamConverter("categoryVO", converter="fos_rest.request_body")
     * @ParamConverter("category", class="App\Entity\Category", options={"mapping": {"category_id" : "id"}})
     * @param CategoryEditVO                       $categoryVO
     * @param ConstraintViolationListInterface $validationErrors
     * @param Category|null $category
     *
     * @return View
     * @throws \Doctrine\ORM\ORMException
     */
    public function edit(CategoryEditVO $categoryVO, ConstraintViolationListInterface $validationErrors, Category $category = null): View
    {
        if (empty($category)) {
            return View::create(['success'=>false, 'message' => 'Category not found'],Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        $this->denyAccessUnlessGranted(ManageCategoryVoter::ADD, $category);
        if (\count($validationErrors) > 0) {
            return View::create($validationErrors,Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        /*header('Content-Type: cli');
        dump($categoryVO);
        dump($category);
        die;/**/
        $category = $this->blogService->updateCategoryByVO($category,$categoryVO);
        
        $this->blogService->saveCategory($category);
        
        $categoryUrl = $this->generateUrl(
            'api_category_show',
            ['id' => $category->getId()]
        );
        return View::createRedirect($categoryUrl);
    }
    
    /**
     * @Rest\Delete("/category/{category_id}", name="api_category_delete")
     * @ParamConverter("category", class="App\Entity\Category", options={"mapping": {"category_id" : "id"}})
     * @param Category                       $category
     *
     * @return View
     */
    public function delete(Category $category=null)
    {
        if (empty($category)) {
            return View::create(['success'=>false, 'message' => 'Category not found'],Response::HTTP_BAD_REQUEST)->setFormat('json');
        }
        try {
            $name = $category->getName();
            $this->blogService->removeCategory($category);
        }catch (\Exception $e){
            return View::create(['success'=>false, 'message' => $e->getMessage() ],Response::HTTP_OK)->setFormat('json');
        }
        return View::create(['success'=>true, 'message' => 'Category '.$name.' removed successfully!'  ],Response::HTTP_OK)->setFormat('json');
    }
}
