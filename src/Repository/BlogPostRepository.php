<?php

namespace App\Repository;

use App\Entity\BlogPost;
use App\Utils\PagerTrait;
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
    use PagerTrait;

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
     * @param bool $withAuthor
     * @param bool $withCategory
     * @param bool $addSelectAuthor
     * @param bool $active
     * @param int  $page
     * @param int  $limit
     *
     * @return Paginator
     */
    public function getAllPostPaginator($withAuthor = false, $withCategory = false, $addSelectAuthor = true, $active = true, $page = 1, $limit = 20)
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildPostsQuery($withAuthor,$withCategory,$addSelectAuthor,$active);
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }
    /**
     * @param bool $withAuthor
     * @param bool $withCategory
     * @param bool $active
     * @param int  $page
     * @param int  $limit
     * @param bool $returnQuery
     *
     * @return Query|mixed|BlogPost[]
     */
    public function findAllPosts($withAuthor = false, $withCategory = false, $active = true, $page = 1, $limit = 100,$returnQuery = false)
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);

        $qb =  $this->buildPostsQuery($withAuthor,$withCategory,$active);
        $qb->setMaxResults($limit)->setFirstResult($offset);

        if($returnQuery){
            return $qb;
        }
        return $this->returnAll($qb);
    }

    public function findAllPostsCount($withAuthor = false, $withCategory = false, $active = true)
    {
        $qb =  $this->buildPostsQuery($withAuthor,$withCategory,false,$active);
        $qb->select('COUNT(' . $this->alias . ')');
        return $this->returnAll($qb,Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param bool $withAuthor
     * @param bool $withCategory
     * @param bool $addSelectAuthor
     * @param bool $active
     *
     * @return QueryBuilder
     */
    public function buildPostsQuery($withAuthor = false, $withCategory = false, $addSelectAuthor = true, $active = true)
    {
        $qb =  $this->createQueryBuilder($this->alias);
        if($withAuthor){
            $this->joinAuthor($qb,$addSelectAuthor);
        }
        if($withCategory){
            $this->joinCategory($qb,$addSelectAuthor);
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

    private function joinAuthor(QueryBuilder $qb, $addSelect = true)
    {
        $qb->leftJoin($this->alias.'.author','a');
        if($addSelect) {
            $qb->addSelect('a');
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
