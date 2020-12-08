<?php

namespace App\Repository;

use App\Entity\StudentDuo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentDuo|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentDuo|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentDuo[]    findAll()
 * @method StudentDuo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentDuoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentDuo::class);
    }

    // /**
    //  * @return StudentDuo[] Returns an array of StudentDuo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StudentDuo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
