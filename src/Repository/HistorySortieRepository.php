<?php

namespace App\Repository;

use App\Entity\HistorySortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistorySortie>
 *
 * @method HistorySortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistorySortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistorySortie[]    findAll()
 * @method HistorySortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistorySortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistorySortie::class);
    }

//    /**
//     * @return HistorySortie[] Returns an array of HistorySortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistorySortie
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
