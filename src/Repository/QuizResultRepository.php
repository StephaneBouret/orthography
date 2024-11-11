<?php

namespace App\Repository;

use App\Entity\QuizResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizResult>
 */
class QuizResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResult::class);
    }

    /**
     * Récupère tous les résultats de quiz d'un utilisateur.
     *
     * @param [type] $user
     * @return QuizResult[] Un tableau d'objets QuizResult
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('qr')
            ->andWhere('qr.user = :user')
            ->setParameter('user', $user)
            ->orderBy('qr.completedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return QuizResult[] Returns an array of QuizResult objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?QuizResult
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}