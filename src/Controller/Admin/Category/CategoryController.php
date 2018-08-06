<?php

namespace App\Controller\Admin\Category;

use App\Entity\Category;
use App\Form\Admin\Category\CategoryType;
use App\Utils\FileUploader;
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
     * @Route("/", name="admin_category_index", methods="GET")
     */
    public function index(): Response
    {
        $categoryRepository =  $this->getDoctrine()->getRepository('App:Category');
        return $this->render('admin/category/index.html.twig', ['categories' => $categoryRepository->findAll()]);
    }

    /**
     * @Route("/new", name="admin_category_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_category_show", methods="GET")
     */
    public function show(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', ['category' => $category]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_edit", methods="GET|POST")
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('admin_category_index');
    }

    /**
     * @Route("/delete/image/{id}", name="admin_category_delete_image", methods="GET")
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
