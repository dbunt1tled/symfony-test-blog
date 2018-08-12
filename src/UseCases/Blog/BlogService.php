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
use App\Entity\Category;
use App\Entity\Image;
use App\Repository\AuthorRepository;
use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManager;

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
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(EntityManager $em/*, BlogPostRepository $postRepository, AuthorRepository $authorRepository, ImageRepository $imageRepository, CategoryRepository $categoryRepository/**/)
    {
        $this->postRepository = $em->getRepository('App:BlogPost');/*$postRepository;/**/
        $this->authorRepository = $em->getRepository('App:Author');/*$authorRepository;/**/
        $this->imageRepository = $em->getRepository('App:Image');/*$imageRepository;/**/
        $this->categoryRepository = $em->getRepository('App:Category');/*$categoryRepository;/**/
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
        $this->postRepository->save($post);
        return $post;
    }

    /**
     * @param BlogPost $post
     *
     * @return BlogPost
     * @throws \Doctrine\ORM\ORMException
     */
    public function savePost(BlogPost $post)
    {
        $this->postRepository->save($post);
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

    /**
     * @param Author $author
     *
     * @return Author
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveAuthor(Author $author)
    {
        $this->authorRepository->save($author);
        return $author;
    }

    /**
     * @param Author $author
     * @param array    $data
     *
     * @return Author
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateAuthor(Author $author, array $data)
    {
        $this->authorRepository->update($author, $data);
        return $author;
    }

    /**
     * @param Author $author
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeAuthor(Author $author)
    {
        return $this->authorRepository->remove($author);
    }
    /**
     * @param Image $image
     *
     * @return Image
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveImage(Image $image)
    {
        $this->imageRepository->save($image);
        return $image;
    }
    /**
     * @param Image $image
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeImage(Image $image)
    {
        return $this->imageRepository->remove($image);
    }

    /**
     * @param Category $category
     *
     * @return Category
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveCategory(Category $category)
    {
        $this->categoryRepository->save($category);
        return $category;
    }
    /**
     * @param Category $category
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeCategory(Category $category)
    {
        return $this->categoryRepository->remove($category);
    }


    public function getAllPostPaginator($withAuthor = false, $withCategory = false, $addSelectAuthor = true, $active = true, $page = 1, $limit = 20)
    {
        $posts = $this->postRepository->getAllPostPaginator($withAuthor, $withCategory, $addSelectAuthor, $active, $page, $limit);
        $totalItems = count($posts);
        return [
            'posts' => $posts,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllCategoryPaginator($withParent = false, $addSelect = true, $active = true, $page = 1, $limit = 20)
    {
        $categories = $this->categoryRepository->getAllCategoryPaginator($withParent, $addSelect, $active, $page, $limit);
        $totalItems = count($categories);
        return [
            'categories' => $categories,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllAuthorPaginator($page = 1, $limit = 20)
    {
        $authors = $this->authorRepository->getAllAuthorPaginator($page, $limit);
        $totalItems = count($authors);
        return [
            'authors' => $authors,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }

}