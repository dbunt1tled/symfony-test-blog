<?php

namespace App\Controller\Admin\Author;

use App\Entity\Author;
use App\Form\Admin\Author\AuthorType;
use App\UseCases\Blog\BlogService;
use App\UseCases\Images\ImageUploader;
use App\Utils\Globals;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("administrator/author")
 */
class AuthorController extends Controller
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
     * @Route("/", name="admin_author_index", methods="GET")
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->query->get('page',1);
        $categoryData = $this->blogService->getAllAuthorPaginator($page,Globals::getPaginatorPageSize());
        return $this->render('admin/author/index.html.twig', [
            'authors' => $categoryData['authors'],
            'totalItems' => $categoryData['totalItems'],
            'page' => $page,
            'pagesCount' => $categoryData['pagesCount'],
        ]);
    }

    /**
     * @Route("/new", name="admin_author_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploader->uploadFile($author);
            $this->blogService->saveAuthor($author);
            return $this->redirectToRoute('admin_author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_author_show", methods="GET")
     * @param Author $author
     *
     * @return Response
     */
    public function show(Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', ['author' => $author]);
    }

    /**
     * @Route("/{id}/edit", name="admin_author_edit", methods="GET|POST")
     * @param Request $request
     * @param Author  $author
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploader->uploadFile($author);
            $this->blogService->saveAuthor($author);

            return $this->redirectToRoute('admin_author_edit', ['id' => $author->getId()]);
        }

        return $this->render('admin/author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_author_delete", methods="DELETE")
     * @param Request $request
     * @param Author  $author
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Request $request, Author $author): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $this->blogService->removeAuthor($author);
        }

        return $this->redirectToRoute('admin_author_index');
    }
}
