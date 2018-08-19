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
     * @param string $name
     * @param string $description
     * @param string $body
     * @param Author $author
     *
     * @return BlogPost
     * @throws \Doctrine\ORM\ORMException
     */
    public function addPost(string $name,string $description, string $body, Author $author)
    {
        $post = new BlogPost();
        $post->setName($name)
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
     * @return Image
     * @throws \Doctrine\ORM\ORMException
     */
    public function persistImage(Image $image)
    {
        $this->imageRepository->persist($image);
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


    public function getAllPostPaginator($withAuthor = false, $withCategory = false, $withImages = false, $addSelectAuthor = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostPaginator($withAuthor, $withCategory,$withImages, $addSelectAuthor, $active, $page, $limit, $orderBy);
        $totalItems = count($posts);
        return [
            'posts' => $posts,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllPostByCategoryPaginator(Category $category, $withAuthor = false, $withCategory = false, $withImages =false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostByCategoryPaginator($category, $withAuthor, $withCategory, $withImages, $addSelect, $active, $page, $limit, $orderBy);
        $totalItems = count($posts);
        return [
            'posts' => $posts,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllPostByAuthorPaginator(Author $author, $withAuthor = false, $withCategory = false, $withImages =false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostByAuthorPaginator($author, $withAuthor, $withCategory, $withImages, $addSelect, $active, $page, $limit, $orderBy);
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

    /**
     * @param string $slug
     *
     * @return BlogPost|null
     */
    public function findOnePostBySlug($slug)
    {
        return $this->postRepository->findOneBy(['slug' => $slug]);
    }
    /**
     * @param string $slug
     *
     * @return Category|null
     */
    public function findOneCategoryBySlug($slug)
    {
        return $this->categoryRepository->findOneBy(['slug' => $slug]);
    }
    /**
     * @param string $slug
     *
     * @return Author|null
     */
    public function findOneAuthorBySlug($slug)
    {
        return $this->authorRepository->findOneBy(['slug' => $slug]);
    }

    public function getAllCategoryTree()
    {
        return $this->categoryRepository->getAllCategoryTree();
    }
    public function childrenHierarchy($node = null, $direct = false, array $options = array(), $includeNode = false)
    {
        return $this->categoryRepository->childrenHierarchy($node,$direct,$options,$includeNode);
    }
}