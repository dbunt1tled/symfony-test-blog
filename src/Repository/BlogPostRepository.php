<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\BlogPost;
use App\Entity\Category;
use App\Utils\PagerTrait;
use App\Utils\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    use PagerTrait,RepositoryTrait;

    private $alias = 'bp';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @param BlogPost $post
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(BlogPost $post)
    {
        $this->_em->persist($post);
        $this->_em->flush();
    }

    /**
     * @param BlogPost $post
     * @param array    $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(BlogPost $post,array $data)
    {
        foreach ($data as $key => $value){
            $key = Inflector::camelize($key);
            if (property_exists($post, $key)) {
                $post->{'set'.ucfirst($key)}($value);
            }
        }
        $this->_em->persist($post);
        $this->_em->flush();
    }
    /**
     * @param BlogPost $post
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(BlogPost $post)
    {
        $this->_em->remove($post);
        $this->_em->flush();
    }

    /**
     * @param bool  $withUser
     * @param bool  $withCategory
     * @param bool  $withImages
     * @param bool  $addSelect
     * @param bool  $active
     * @param int   $page
     * @param int   $limit
     * @param array $orderBy
     *
     * @return Paginator
     */
    public function getAllPostPaginator($withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildPostsQuery($withUser,$withCategory,$withImages,$withTags,$addSelect,$active);
        $qb =  $this->addOrderByArray($qb,$orderBy,$this->alias);
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }

    /**
     * @param Category $category
     * @param bool  $withUser
     * @param bool  $withCategory
     * @param bool  $withImages
     * @param bool  $addSelect
     * @param bool  $active
     * @param int   $page
     * @param int   $limit
     * @param array $orderBy
     *
     * @return Paginator
     */
    public function getAllPostByCategoryPaginator(Category $category, $withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildPostsQuery($withUser,$withCategory,$withImages,$withTags,$addSelect,$active);
        $qb =  $this->addOrderByArray($qb,$orderBy,$this->alias);
        $qb->andWhere($this->alias.'.category = :category')
           ->setParameter('category', $category);
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }
    /**
     * @param Tag $tag
     * @param bool  $withUser
     * @param bool  $withCategory
     * @param bool  $withImages
     * @param bool  $addSelect
     * @param bool  $active
     * @param int   $page
     * @param int   $limit
     * @param array $orderBy
     *
     * @return Paginator
     */
    public function getAllPostByTagPaginator(Tag $tag, $withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildPostsQuery($withUser,$withCategory,$withImages,$withTags,$addSelect,$active);
        $qb =  $this->addOrderByArray($qb,$orderBy,$this->alias);
        $qb->andWhere(':tag MEMBER OF '.$this->alias.'.tags')
           ->setParameter('tag', $tag);
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }

    public function getAllPostByUserPaginator(User $user, $withUser = false, $withCategory = false, $withImages =false, $withTags = false, $addSelect = true, $active = true, $page = 1, $limit = 20, $orderBy = [])
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildPostsQuery($withUser,$withCategory,$withImages,$withTags,$addSelect,$active);
        $qb =  $this->addOrderByArray($qb,$orderBy,$this->alias);
        $qb->andWhere($this->alias.'.user = :user')
           ->setParameter('user',$user);
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }
    /**
     * @param bool $withUser
     * @param bool $withCategory
     * @param bool $active
     * @param int  $page
     * @param int  $limit
     * @param bool $returnQuery
     *
     * @return Query|mixed|BlogPost[]
     */
    public function findAllPosts($withUser = false, $withCategory = false, $withImages =false, $withTags = false, $active = true, $page = 1, $limit = 100,$returnQuery = false)
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);

        $qb =  $this->buildPostsQuery($withUser,$withCategory,$withImages,$withTags,$active);
        $qb->setMaxResults($limit)->setFirstResult($offset);

        if($returnQuery){
            return $qb;
        }
        return $this->returnAll($qb);
    }

    public function findAllPostsCount($withUser = false, $withCategory = false, $withImages =false, $withTags = false, $active = true)
    {
        $qb =  $this->buildPostsQuery($withUser,$withCategory,$withImages,$withTags,false,$active);
        $qb->select('COUNT(' . $this->alias . ')');
        return $this->returnAll($qb,Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param bool $withUser
     * @param bool $withCategory
     * @param bool $withImages
     * @param bool $addSelect
     * @param bool $active
     *
     * @return QueryBuilder
     */
    public function buildPostsQuery($withUser = false, $withCategory = false,$withImages =false, $withTags = false, $addSelect = true, $active = true)
    {
        $qb =  $this->createQueryBuilder($this->alias);
        if($withUser){
            $this->joinUser($qb,$addSelect);
        }
        if($withCategory){
            $this->joinCategory($qb,$addSelect);
        }
        if($withImages){
            $this->joinImages($qb,$addSelect);
        }
        if($withTags){
            $this->joinTags($qb,$addSelect);
        }
        if($active){
            $this->active($qb);
        }
        return $qb;
    }

    private function returnAll(QueryBuilder $qb, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        return  $qb->getQuery()->getResult($hydrationMode);
    }

    private function active(QueryBuilder $qb)
    {
        return $qb->andWhere($this->alias.'.status = :activeStatus')
                    ->setParameter('activeStatus', BlogPost::STATUS_ACTIVE);
    }

    private function joinUser(QueryBuilder $qb, $addSelect = true)
    {
        $qb->leftJoin($this->alias.'.user','u');
        if($addSelect) {
            $qb->addSelect('u');
        }
        return $qb;
    }
    private function joinImages(QueryBuilder $qb, $addSelect = true)
    {
        $qb->leftJoin($this->alias.'.images','i');
        if($addSelect) {
            $qb->addSelect('i');
        }
        return $qb;
    }
    private function joinCategory(QueryBuilder $qb, $addSelect = true)
    {
        $qb->leftJoin($this->alias.'.category','c');
        if($addSelect) {
            $qb->addSelect('c');
        }
        return $qb;
    }
    private function joinTags(QueryBuilder $qb, $addSelect = true)
    {
        $qb->leftJoin($this->alias.'.tags','t');
        if($addSelect) {
            $qb->addSelect('t');
        }
        return $qb;
    }

//    /**
//     * @return BlogPost[] Returns an array of BlogPost objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlogPost
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
