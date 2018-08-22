<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\Admin\User\UserType;
use App\UseCases\Blog\BlogService;
use App\UseCases\Images\ImageUploader;
use App\Utils\Globals;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("administrator/user")
 */
class UserController extends Controller
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
     * @Route("/", name="admin_user_index", methods="GET")
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->query->get('page',1);
        $categoryData = $this->blogService->getAllUserPaginator($page,Globals::getPaginatorPageSize());
        return $this->render('admin/user/index.html.twig', [
            'users' => $categoryData['users'],
            'totalItems' => $categoryData['totalItems'],
            'page' => $page,
            'pagesCount' => $categoryData['pagesCount'],
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploader->uploadFile($user);
            $this->blogService->saveUser($user);
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods="GET")
     * @param User $user
     *
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods="GET|POST")
     * @param Request $request
     * @param User  $user
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploader->uploadFile($user);
            $this->blogService->saveUser($user);

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods="DELETE")
     * @param Request $request
     * @param User  $user
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->blogService->removeUser($user);
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
