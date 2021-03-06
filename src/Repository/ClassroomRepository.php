<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Entity\Classroom;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Classroom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classroom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classroom[]    findAll()
 * @method Classroom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassroomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classroom::class);
    }

    public function getClassrooms($user, $language)
    {

        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.teachers', 't')
            ->where('t.id <> :user')
            ->andWhere('t.language <> :language')
            ->setParameter(":user", $user)
            ->setParameter(":language", $language)
            // ->andWhere('c.grade = :grade')
            // ->setParameter(":grade", $grade)
        ;


        return $query->getQuery()->getResult();
    }

    public function getClassroomsUser($user)
    {

        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.teachers', 't')
            ->where('t.id = :user')
            ->setParameter(":user", $user);
        return $query->getQuery()->getResult();
    }

    /**
     * @return Classroom[] Returns an array of Classroom objects
     */

    // public function findByid($user)
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.id = :val')
    //         ->setParameter('val', $user)
    //         ->orderBy('c.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult();
    // }

    /*
    public function findOneBySomeField($value): ?Classroom
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
