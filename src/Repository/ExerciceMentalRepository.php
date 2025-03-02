<?php

namespace App\Repository;

use App\Entity\ExerciceMental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExerciceMental>
 */
class ExerciceMentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciceMental::class);
    }
    public function findBySearchAndSort(string $search, string $sortField, string $sortDirection): array
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($search)) {
            $qb->andWhere('e.titre LIKE :search OR e.description LIKE :search OR e.objectif LIKE :search')
            ->setParameter('search', '%'.$search.'%');
        }

        // Ensure the sort field is one of the allowed values
        $allowedSortFields = ['titre', 'description', 'objectif'];
        if (in_array($sortField, $allowedSortFields)) {
            $qb->orderBy('e.' . $sortField, $sortDirection);
        } else {
            // Fallback sorting (for example, by ID)
            $qb->orderBy('e.id', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }


    //    /**
    //     * @return ExerciceMental[] Returns an array of ExerciceMental objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ExerciceMental
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
