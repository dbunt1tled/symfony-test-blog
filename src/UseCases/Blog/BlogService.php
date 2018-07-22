<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 22.07.18
 * Time: 11:33
 */

namespace App\UseCases\Blog;

use App\Entity\Author;
use App\Entity\BlogPost;
use App\Repository\AuthorRepository;
use App\Repository\BlogPostRepository;

class BlogService
{

    /**
     * @var BlogPostRepository
     */
    private $postRepository;
    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    public function __construct(BlogPostRepository $postRepository, AuthorRepository $authorRepository)
    {

        $this->postRepository = $postRepository;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $body
     * @param Author $author
     *
     * @return BlogPost
     * @throws \Doctrine\ORM\ORMException
     */
    public function addPost(string $title,string $description, string $body, Author $author)
    {
        $post = new BlogPost();
        $post->setTitle($title)
            ->setDescription($description)
            ->setBody($body)
            ->setAuthor($author);
        $this->postRepository->add($post);
        return $post;
    }

    /**
     * @param BlogPost $post
     * @param array    $data
     *
     * @return BlogPost
     * @throws \Doctrine\ORM\ORMException
     */
    public function updatePost(BlogPost $post, array $data)
    {
        $this->postRepository->update($post, $data);
        return $post;
    }

    /**
     * @param array $condition
     *
     * @return BlogPost|null
     */
    public function findOnePost(array $condition)
    {
        return $this->postRepository->findOneBy($condition);
    }

    /**
     * @param BlogPost $post
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removePost(BlogPost $post)
    {
        return $this->postRepository->remove($post);
    }
}