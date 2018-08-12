<?php

namespace App\Controller\Admin\BlogPost;

use App\Entity\BlogPost;
use App\Entity\Image;
use App\Form\Admin\BlogPost\BlogPostType;
use App\UseCases\Blog\BlogService;
use App\Utils\Globals;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("administrator/blog-post")
 */
class BlogPostController extends Controller
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
     * @Route("/", name="admin_blog_post_index", methods="GET")
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = (int)$request->query->get('page',1);
        $postData = $this->blogService->getAllPostPaginator(true,true,true,false,$page,Globals::getPaginatorPageSize());

        return $this->render('admin/blog_post/index.html.twig', [
            'posts' => $postData['posts'],
            'totalItems' => $postData['totalItems'],
            'page' => $page,
            'pagesCount' => $postData['pagesCount'],
        ]);
    }

    /**
     * @Route("/new", name="admin_blog_post_new", methods="GET|POST")
     * @param Request $request
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function new(Request $request): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->blogService->savePost($blogPost);
            return $this->redirectToRoute('admin_blog_post_index');
        }

        return $this->render('admin/blog_post/new.html.twig', [
            'blog_post' => $blogPost,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_blog_post_show", methods="GET")
     * @param BlogPost $blogPost
     *
     * @return Response
     */
    public function show(BlogPost $blogPost): Response
    {
        return $this->render('admin/blog_post/show.html.twig', ['blog_post' => $blogPost]);
    }

    /**
     * @Route("/{id}/edit", name="admin_blog_post_edit", methods="GET|POST")
     * @param Request  $request
     * @param BlogPost $blogPost
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function edit(Request $request, BlogPost $blogPost): Response
    {

        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attachment = $blogPost->getUploadedFile();
            if($attachment instanceof UploadedFile) {
                $image = new Image();
                $image->setTitle($blogPost->getTitle());
                $image->setFile($attachment);
                $this->blogService->saveImage($image);
                $blogPost->addImage($image);
            }
            $this->blogService->savePost($blogPost);
            return $this->redirectToRoute('admin_blog_post_edit', ['id' => $blogPost->getId()]);
        }

        return $this->render('admin/blog_post/edit.html.twig', [
            'blog_post' => $blogPost,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_blog_post_delete", methods="DELETE")
     * @param Request  $request
     * @param BlogPost $blogPost
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Request $request, BlogPost $blogPost): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blogPost->getId(), $request->request->get('_token'))) {
            $this->blogService->removePost($blogPost);
        }

        return $this->redirectToRoute('admin_blog_post_index');
    }

    /**
     * @Route("/{blog_post_id}/delete/image/{image_id}", name="admin_blog_post_delete_image", methods="GET")
     *
     * @ParamConverter("blogPost", class="App\Entity\BlogPost", options={"mapping": {"blog_post_id" : "id"}})
     * @ParamConverter("image", class="App\Entity\Image", options={"mapping": {"image_id" : "id"}})
     * @param Request  $request
     * @param BlogPost $blogPost
     * @param Image    $image
     *
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteImage(Request $request,BlogPost $blogPost, Image $image): Response
    {
        /*if ($this->isCsrfTokenValid('delete'.$blogPost->getId(), $request->request->get('_token'))) {

        }/**/
        $this->blogService->removeImage($image);
        return $this->redirectToRoute('admin_blog_post_edit', ['id' => $blogPost->getId()]);
    }
}
