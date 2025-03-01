<?php

namespace App\Repository;

use App\Entity\CentreDeDon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CentreDeDon>
 */
class CentreDeDonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CentreDeDon::class);
    }

    //    /**
    //     * @return CentreDeDon[] Returns an array of CentreDeDon objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CentreDeDon
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // In CentreDeDonRepository.php
    public function findAllCentres()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')  // Adjust 'name' to the field you want to use for sorting
            ->getQuery()
            ->getResult();
    }

}
