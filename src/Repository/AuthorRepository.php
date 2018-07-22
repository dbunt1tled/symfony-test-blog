<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(RegistryInterface $registry, EntityManager $em )
    {
        parent::__construct($registry, Author::class);
        $this->em = $em;
    }

    /**
     * @param Author $author
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(Author $author)
    {
        $this->em->persist($author);
        $this->em->flush();
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
        $this->em->persist($author);
        $this->em->flush();
    }
    /**
     * @param Author $author
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(Author $author)
    {
        $this->em->remove($author);
        $this->em->flush();
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
}
