<?php

namespace App\Repository;

use App\Entity\Author;
use App\Utils\PagerTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    use PagerTrait;

    private $alias = 'ar';
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param Author $author
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Author $author)
    {
        $this->_em->persist($author);
        $this->_em->flush();
    }

    /**
     * @param Author $author
     * @param array    $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Author $author,array $data)
    {
        foreach ($data as $key => $value){
            $key = Inflector::camelize($key);
            if (property_exists($author, $key)) {
                $author->{'set'.ucfirst($key)}($value); // ucfirst() is not required but I think it's cleaner
            }
        }
        $this->_em->persist($author);
        $this->_em->flush();
    }
    /**
     * @param Author $author
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(Author $author)
    {
        $this->_em->remove($author);
        $this->_em->flush();
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getAllIds(): ?array
    {
        $result =  $this->createQueryBuilder($this->alias)
                        ->select($this->alias.'.id')
                        ->getQuery()
                        ->getScalarResult();
        return array_column($result, "id");;
    }
    public function getAllAuthorPaginator($page = 1, $limit = 20)
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildCategoryQuery();
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }

    public function buildCategoryQuery()
    {
        $qb =  $this->createQueryBuilder($this->alias);
        /*if($withParent) {
            $this->joinParent($qb, $addSelect);
        }
        if($active){
            $this->active($qb);
        }/**/
        return $qb;
    }

    private function returnAll(QueryBuilder $qb, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        return  $qb->getQuery()->getResult($hydrationMode);
    }
    /*
    private function active(QueryBuilder $qb)
    {
        return $qb->andWhere($this->alias.'.status = :activeStatus')
                  ->setParameter('activeStatus', Category::STATUS_ACTIVE);
    }
    private function joinParent(QueryBuilder $qb, $addSelect = true)
    {
        $qb->leftJoin($this->alias.'.parent','pc');
        if($addSelect) {
            $qb->addSelect('pc');
        }
        return $qb;
    }
    /**/
}
