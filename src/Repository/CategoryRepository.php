<?php

namespace App\Repository;

use App\Entity\Category;
use App\Utils\PagerTrait;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;


/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends NestedTreeRepository/*ServiceEntityRepository /**/
{
    use PagerTrait;

    private $alias = 'ct';

    /*
    public function __construct(RegistryInterface $registry)
    {
        $entityClass = Category::class;
        $manager = $registry->getManagerForClass($entityClass);
        //$registry->getEntityManager()
        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }/**

    /**
     * @param Category $category
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Category $category)
    {
        $this->_em->persist($category);
        $this->_em->flush();
    }

    /**
     * @param Category $category
     * @param array    $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Category $category,array $data)
    {
        foreach ($data as $key => $value){
            $key = Inflector::camelize($key);
            if (property_exists($category, $key)) {
                $category->{'set'.ucfirst($key)}($value);
            }
        }
        $this->_em->persist($category);
        $this->_em->flush();
    }
    /**
     * @param Category $category
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(Category $category)
    {
        $this->_em->remove($category);
        $this->_em->flush();
    }

    public function getAllCategoryPaginator($withParent = false, $addSelect = false, $active = true, $page = 1, $limit = 20)
    {
        $page = $this->getPage($page);
        $limit = $this->getLimit($limit);
        $offset = $this->getOffset($page, $limit);
        $qb = $this->buildCategoryQuery($withParent, $addSelect, $active);
        $paginator = new Paginator($qb, $fetchJoinCollection = true);
        $paginator->getQuery()
                  ->setFirstResult($offset)
                  ->setMaxResults($limit);
        return  $paginator;
    }

    public function buildCategoryQuery($withParent = false, $addSelect = false, $active = true)
    {
        $qb =  $this->createQueryBuilder($this->alias);
        if($withParent) {
            $this->joinParent($qb, $addSelect);
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
    /*
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }/**/

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
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
}
