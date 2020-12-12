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


    public function getClassroomDuo()
    {

        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.teachers', 't')
            ->where('t.id <> :user')
            ->andWhere('t.language <> :language')
            ->setParameter(":user", $user)
            // ->andWhere('c.grade = :grade')
            // ->setParameter(":grade", $grade)
        ;


        return $query->getQuery()->getResult();
    }

    public function getClassroomDuoTeacher($id)
    {

        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.classroom_1 = :id')
            ->orwhere('c.classroom_2 = :id')
            ->setParameter(":id", $id);

        return $query->getQuery()->getResult();
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
