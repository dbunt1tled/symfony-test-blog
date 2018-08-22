<?php

namespace App\Repository;

use App\Entity\User;
use App\Utils\PagerTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    use PagerTrait;

    private $alias = 'u';
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param User $user
     * @param array    $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(User $user,array $data)
    {
        foreach ($data as $key => $value){
            $key = Inflector::camelize($key);
            if (property_exists($user, $key)) {
                $user->{'set'.ucfirst($key)}($value); // ucfirst() is not required but I think it's cleaner
            }
        }
        $this->_em->persist($user);
        $this->_em->flush();
    }
    /**
     * @param User $user
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(User $user)
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

//    /**
//     * @return User[] Returns an array of Users objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
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
    public function getAllUserPaginator($page = 1, $limit = 20)
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

    /**
     * @param string $username
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername(string $username)
    {
        $result = $this->createQueryBuilder($this->alias)
            ->where('u.name = :name OR u.email = :email')
            ->setParameter('name', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getSingleResult();
        return $result;
    }
}
