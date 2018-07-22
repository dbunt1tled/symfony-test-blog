<?php

namespace App\Controller;

use App\UseCases\Blog\BlogService;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Stringy\StaticStringy as S;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{

    /**
     * @var BlogService
     */
    private $blogService;
    private $pageSize = 10;
    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * @Route("/blog", name="blog")
     * @param Request $request
     * @return Response
     *
     */
    public function index(Request $request)
    {
        $page = (int)$request->query->get('page',1);
        $posts = $this->getDoctrine()->getRepository('App:BlogPost')->getAllPostPaginator(true,true,false,$page,$this->pageSize);
        $totalItems = count($posts);
        $pagesCount = ceil($totalItems / $this->pageSize);
        return $this->render('blog/index.html.twig', compact('posts','totalItems','pagesCount','page'));
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
        try{
            $this->blogService->addPost(S::safeTruncate($text,10),S::safeTruncate($text,25),$text,$this->getUser());
        }catch (\Exception $exception) {
            $this->addFlash('danger','Something went wrong: '.$exception->getMessage());
        }

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
        $post = $this->blogService->findOnePost(['id'=> $id, 'author' => $this->getUser()]);
        if(!$post) {
            $this->addFlash('danger','Something went wrong! Post not found.');
            return $this->redirectToRoute('blog');
        }
        try{
            $this->blogService->updatePost($post,['title'=> S::safeTruncate($text,10),'description' => S::safeTruncate($text,25),'body' => $text]);
        }catch (\Exception $exception) {
            $this->addFlash('danger','Something went wrong: '.$exception->getMessage());
        }
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
        $post = $this->blogService->findOnePost(['id'=> $id, 'author' => $this->getUser()]);
        if(!$post) {
            $this->addFlash('danger','Something went wrong! Post not found.');
            return $this->redirectToRoute('blog');
        }
        try{
            $this->blogService->removePost($post);
        }catch (\Exception $exception) {
            $this->addFlash('danger','Something went wrong: '.$exception->getMessage());
        }
        return $this->redirectToRoute('blog');
    }
}
