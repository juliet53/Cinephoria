<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    public function findFilmsAddedLastWednesday(): array
{
    $today = new \DateTimeImmutable('today');
    $dayOfWeek = $today->format('w'); 

    $daysToSubtract = ($dayOfWeek >= 3) ? $dayOfWeek - 3 : 7 - (3 - $dayOfWeek);
    $lastWednesday = $today->modify("-$daysToSubtract days");

    $start = $lastWednesday->setTime(0, 0);
    $end = $lastWednesday->setTime(23, 59, 59);

    return $this->createQueryBuilder('f')
        ->andWhere('f.createdAt BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('f.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}


    //    /**
    //     * @return Film[] Returns an array of Film objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Film
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
