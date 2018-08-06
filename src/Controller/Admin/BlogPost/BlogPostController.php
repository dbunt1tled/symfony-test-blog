<?php

namespace App\Controller\Admin\BlogPost;

use App\Entity\BlogPost;
use App\Entity\Image;
use App\Form\Admin\BlogPost\BlogPostType;
use App\Repository\BlogPostRepository;
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
     * @Route("/", name="admin_blog_post_index", methods="GET")
     */
    public function index(BlogPostRepository $blogPostRepository): Response
    {
        return $this->render('admin/blog_post/index.html.twig', ['blog_posts' => $blogPostRepository->findAll()]);
    }

    /**
     * @Route("/new", name="admin_blog_post_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();

            return $this->redirectToRoute('admin_blog_post_index');
        }

        return $this->render('admin/blog_post/new.html.twig', [
            'blog_post' => $blogPost,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_blog_post_show", methods="GET")
     */
    public function show(BlogPost $blogPost): Response
    {
        return $this->render('admin/blog_post/show.html.twig', ['blog_post' => $blogPost]);
    }

    /**
     * @Route("/{id}/edit", name="admin_blog_post_edit", methods="GET|POST")
     */
    public function edit(Request $request, BlogPost $blogPost): Response
    {

        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $attachment = $blogPost->getUploadedFile();
            if($attachment instanceof UploadedFile) {
                $image = new Image();
                $image->setTitle($blogPost->getTitle());
                $image->setFile($attachment);
                $em->persist($image);
                $blogPost->addImage($image);
            }
            $em->persist($blogPost);
            $em->flush();
            return $this->redirectToRoute('admin_blog_post_edit', ['id' => $blogPost->getId()]);
        }

        return $this->render('admin/blog_post/edit.html.twig', [
            'blog_post' => $blogPost,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_blog_post_delete", methods="DELETE")
     */
    public function delete(Request $request, BlogPost $blogPost): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blogPost->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($blogPost);
            $em->flush();
        }

        return $this->redirectToRoute('admin_blog_post_index');
    }
}
