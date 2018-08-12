<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Inflector\Inflector;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @param Image $image
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Image $image)
    {
        $this->_em->persist($image);
        $this->_em->flush();
    }

    /**
     * @param Image $image
     * @param array $data
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Image $image,array $data)
    {
        foreach ($data as $key => $value){
            $key = Inflector::camelize($key);
            if (property_exists($image, $key)) {
                $image->{'set'.ucfirst($key)}($value);
            }
        }
        $this->_em->persist($image);
        $this->_em->flush();
    }
    /**
     * @param Image $image
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(Image $image)
    {
        $this->_em->remove($image);
        $this->_em->flush();
    }



    //    /**
//     * @return Image[] Returns an array of Image objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
