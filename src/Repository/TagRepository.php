<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Inflector\Inflector;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    private $alias = 't';
    
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
    }

//    /**
//     * @return Tag[] Returns an array of Tag objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
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
     * @param Tag $tag
     * @param array    $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Tag $tag,array $data)
    {
        foreach ($data as $key => $value){
            $key = Inflector::camelize($key);
            if (property_exists($tag, $key)) {
                $tag->{'set'.ucfirst($key)}($value);
            }
        }
        $this->_em->persist($tag);
        $this->_em->flush();
    }
    /**
     * @param Tag $tag
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(Tag $tag)
    {
        $this->_em->remove($tag);
        $this->_em->flush();
    }
    public function getAllIds(): ?array
    {
        $result =  $this->createQueryBuilder($this->alias)
                        ->select($this->alias.'.id')
                        ->getQuery()
                        ->getScalarResult();
        return array_column($result, "id");
    }
    public function findByIds(array $ids)
    {
        if(empty($ids)){
            return null;
        }
        $result =  $this->createQueryBuilder($this->alias)
                        ->andWhere($this->alias.'.id IN (:ids)')
                        ->setParameter('ids',$ids,Connection::PARAM_INT_ARRAY)
                        ->getQuery()
                        ->getResult();
        return $result;
    }
}
