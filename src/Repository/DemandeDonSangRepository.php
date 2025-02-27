<?php

namespace App\Repository;

use App\Entity\DemandeDonSang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeDonSang>
 */
class DemandeDonSangRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeDonSang::class);
    }

        public function countDonsByMonth(): array
    {
        return $this->createQueryBuilder('d')
            ->select("SUBSTRING(d.createdAt, 1, 7) AS mois, COUNT(d.id) AS total")
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countDonsByGroupeSanguin(): array
    {
        return $this->createQueryBuilder('d')
            ->select("d.groupesanguain AS groupe, COUNT(d.id) AS total")
            ->groupBy('groupe')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countDonsByCentre(): array
    {
        return $this->createQueryBuilder('d')
            ->select("c.name AS centre, COUNT(d.id) AS total")  
            ->join('d.CentreDeDon', 'c')
            ->groupBy('c.id')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findByCentre($centre)
{
    return $this->createQueryBuilder('d')
        ->innerJoin('d.CentreDeDon', 'c')  //
        ->andWhere('c.name = :centre')    
        ->setParameter('centre', $centre)
        ->orderBy('c.name', 'ASC')        
        ->getQuery()
        ->getResult();
}
    
    public function findByStatus($status = null)
    {
        $queryBuilder = $this->createQueryBuilder('d');
        
        if ($status) {
            $queryBuilder->andWhere('d.status = :status')
                ->setParameter('status', $status);
        }
        
        $queryBuilder->orderBy('d.status', 'ASC');
        
        return $queryBuilder->getQuery()->getResult();
    }

        public function findByUser($userId)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('d.createdAt', 'DESC') 
            ->getQuery()
            ->getResult();
    }
  
}
