<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\UseCases\Blog\BlogService;
use App\UseCases\Images\ImageUploader;
use App\Form\Admin\Category\CategoryType;
use App\Utils\Globals;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("administrator/category")
 */
class CategoryController extends Controller
{

    /**
     * @var ImageUploader
     */
    private $uploader;
    /**
     * @var BlogService
     */
    private $blogService;

    public function __construct(ImageUploader $uploader, BlogService $blogService)
    {

        $this->uploader = $uploader;
        $this->blogService = $blogService;
    }

    /**
     * @Route("/", name="admin_category_index", methods="GET")
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->query->get('page',1);
        $categoryData = $this->blogService->getAllCategoryPaginator(true,true,true,$page,Globals::getPaginatorPageSize());
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryData['categories'],
            'totalItems' => $categoryData['totalItems'],
            'page' => $page,
            'pagesCount' => $categoryData['pagesCount'],
        ]);
    }

    /**
     * @Route("/new", name="admin_category_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->uploader->uploadFile($category);
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_category_show", methods="GET")
     * @param Category $category
     *
     * @return Response
     */
    public function show(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_edit", methods="GET|POST")
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploader->uploadFile($category);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_category_delete", methods="DELETE")
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $category->deleteImage();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('admin_category_index');
    }

    /**
     * @Route("/delete/image/{id}", name="admin_category_delete_image", methods="GET")
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     */
    public function deleteImage(Request $request, Category $category): Response
    {
        /*if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {

        }/**/
        $em = $this->getDoctrine()->getManager();
        $category->deleteImage();
        $em->persist($category);
        $em->flush();
        return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
    }

}
