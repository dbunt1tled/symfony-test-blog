<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 22.07.18
 * Time: 11:33
 */

namespace App\UseCases\Blog;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Image;
use App\Repository\Factory\CategoryFactory;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\ValuesObject\Category\CategoryVO;
use Doctrine\ORM\EntityManager;

class BlogService
{

    /**
     * @var BlogPostRepository
     */
    private $postRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var TagRepository
     */
    private $tagRepository;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    public function __construct(EntityManager $em,CategoryFactory $categoryFactory/*, BlogPostRepository $postRepository, UserRepository $userRepository, ImageRepository $imageRepository, CategoryRepository $categoryRepository/**/)
    {
        $this->postRepository = $em->getRepository('App:BlogPost');/*$postRepository;/**/
        $this->userRepository = $em->getRepository('App:User');/*$userRepository;/**/
        $this->imageRepository = $em->getRepository('App:Image');/*$imageRepository;/**/
        $this->categoryRepository = $em->getRepository('App:Category');/*$categoryRepository;/**/
        $this->tagRepository = $em->getRepository('App:Tag');/*$tagRepository;/**/
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $body
     * @param User $user
     *
     * @return BlogPost
     * @throws \Doctrine\ORM\ORMException
     */
    public function addPost(string $name,string $description, string $body, User $user)
    {
        $post = new BlogPost();
        $post->setName($name)
            ->setDescription($description)
            ->setBody($body)
            ->setUser($user);
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
     * @param array $condition
     *
     * @return Image|null
     */
    public function findOneImage(array $condition)
    {
        return $this->imageRepository->findOneBy($condition);
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
     * @param User $user
     *
     * @return User
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveUser(User $user)
    {
        $this->userRepository->save($user);
        return $user;
    }

    /**
     * @param User $user
     * @param array    $data
     *
     * @return User
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateUser(User $user, array $data)
    {
        $this->userRepository->update($user, $data);
        return $user;
    }

    /**
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function removeUser(User $user)
    {
        return $this->userRepository->remove($user);
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


    public function getAllPostPaginator($withUser = false, $withCategory = false, $withImages = false, $withTags = false, $addSelectUser = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostPaginator($withUser, $withCategory,$withImages, $withTags, $addSelectUser, $active, $page, $limit, $orderBy);
        $totalItems = count($posts);
        return [
            'posts' => $posts,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllPostByCategoryPaginator(Category $category, $withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostByCategoryPaginator($category, $withUser, $withCategory, $withImages, $withTags, $addSelect, $active, $page, $limit, $orderBy);
        $totalItems = count($posts);
        return [
            'posts' => $posts,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllPostByTagPaginator(Tag $tag, $withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostByTagPaginator($tag, $withUser, $withCategory, $withImages, $withTags, $addSelect, $active, $page, $limit, $orderBy);
        $totalItems = count($posts);
        return [
            'posts' => $posts,
            'totalItems' => $totalItems,
            'pagesCount' => ceil($totalItems / $limit),
        ];
    }
    public function getAllPostByUserPaginator(User $user, $withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $posts = $this->postRepository->getAllPostByUserPaginator($user, $withUser, $withCategory, $withImages, $withTags, $addSelect, $active, $page, $limit, $orderBy);
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
            'pagesCount' => (int)ceil($totalItems / $limit),
        ];
    }
    public function getAllUserPaginator($page = 1, $limit = 20)
    {
        $users = $this->userRepository->getAllUserPaginator($page, $limit);
        $totalItems = count($users);
        return [
            'users' => $users,
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
     * @return Tag|null
     */
    public function findOneTagBySlug($slug)
    {
        return $this->tagRepository->findOneBy(['slug' => $slug]);
    }
    
    
    /**
     * @param string $slug
     *
     * @return User|null
     */
    public function findOneUserBySlug($slug)
    {
        return $this->userRepository->findOneBy(['slug' => $slug]);
    }

    public function getAllCategoryTree()
    {
        return $this->categoryRepository->getAllCategoryTree();
    }
    public function childrenHierarchy($node = null, $direct = false, array $options = array(), $includeNode = false)
    {
        return $this->categoryRepository->childrenHierarchy($node,$direct,$options,$includeNode);
    }

    public function findAllPosts($withUser = false, $withCategory = false, $withImages =false, $active = true, $page = 1, $limit = 100,$returnQuery = false)
    {
        return $this->postRepository->findAllPosts($withUser, $withCategory, $withImages, $active, $page, $limit,$returnQuery);
    }

    /**
     * @param string $username
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername(string $username)
    {
        return $this->userRepository->loadUserByUsername($username);
    }

    /**
     * @param int $userId
     *
     * @return User|null|object
     */
    public function findUser(int $userId)
    {
        return $this->userRepository->find($userId);
    }
    public function findCategory(int $categoryId)
    {
        return $this->categoryRepository->find($categoryId);
    }
    public function makeCategoryByVO($categoryVO)
    {
        $category = $this->categoryFactory->makeFromArray($categoryVO->toArray());
        return $category;
    }
    public function updateCategoryByVO(Category $category,$categoryVO)
    {
        return $this->categoryFactory->updateFromArray($category,$categoryVO->toArray());
    }
    
    public function categoryBreadcrumbs(Category $category)
    {
        $breadcrumbs = $this->categoryRepository->categoryBreadcrumbs($category);
        return $breadcrumbs;
    }
}