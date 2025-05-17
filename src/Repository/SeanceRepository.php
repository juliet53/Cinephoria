<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }
    public function findByFilters(?string $filmTitle, ?string $date): array
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.film', 'f');

        if ($filmTitle) {
            $qb->andWhere('f.title = :filmTitle')
               ->setParameter('filmTitle', $filmTitle);
        }

        if ($date) {
            // Filtrer par plage de dates (début à fin de journée)
            $startDate = new \DateTime($date);
            $endDate = (clone $startDate)->modify('+1 day');

            $qb->andWhere('s.dateHeureDebut >= :startDate')
               ->andWhere('s.dateHeureDebut < :endDate')
               ->setParameter('startDate', $startDate)
               ->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Seance[] Returns an array of Seance objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Seance
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
