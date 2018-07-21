<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Stringy\StaticStringy as S;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $posts = $this->getDoctrine()->getRepository('App:BlogPost')->findAll();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/blog/create/{text}", name="post_create")
     * @Security("has_role('ROLE_USER')")
     * @param string $text
     * @return Response
     *
     */
    public function create($text)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $post = new BlogPost();
        $post->setTitle(S::safeTruncate($text,10))
            ->setDescription(S::safeTruncate($text,25))
            ->setBody($text)
            ->setAuthor($user);
        $em->persist($post);
        $em->flush();
        return $this->redirectToRoute('blog');
    }

    /**
     * @Route("/blog/update/{id}/{text}", name="post_update")
     * @Security("has_role('ROLE_USER')")
     * @param integer $id
     * @param string $text
     * @return Response
     *
     */
    public function update($id,$text)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $post = $this->getDoctrine()->getRepository('App:BlogPost')->findOneBy(['id'=> $id, 'author' => $user]);

        if(!$post) {
            $this->addFlash('danger','Something Wrong!');
            return $this->redirectToRoute('blog');
        }
        $post->setTitle(S::safeTruncate($text,10))
             ->setDescription(S::safeTruncate($text,25))
             ->setBody($text);
        $em->persist($post);
        $em->flush();
        return $this->redirectToRoute('blog');
    }

    /**
     * @Route("/blog/delete/{id}", name="post_delete")
     * @Security("has_role('ROLE_USER')")
     * @param integer $id
     * @return Response
     *
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $post = $this->getDoctrine()->getRepository('App:BlogPost')->findOneBy(['id'=> $id, 'author' => $user]);

        if(!$post) {
            $this->addFlash('danger','Something Wrong!');
            return $this->redirectToRoute('blog');
        }
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('blog');
    }
}
