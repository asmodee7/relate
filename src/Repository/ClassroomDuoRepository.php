<?php

namespace App\Repository;

use App\Entity\ClassroomDuo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClassroomDuo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassroomDuo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassroomDuo[]    findAll()
 * @method ClassroomDuo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassroomDuoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassroomDuo::class);
    }

    // /**
    //  * @return ClassroomDuo[] Returns an array of ClassroomDuo objects
    //  */
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
    public function findOneBySomeField($value): ?ClassroomDuo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
