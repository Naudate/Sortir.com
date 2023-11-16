<?php

namespace App\Repository;

use App\Entity\HistoryUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryUser>
 *
 * @method HistoryUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryUser[]    findAll()
 * @method HistoryUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryUser::class);
    }

//    /**
//     * @return HistoryUser[] Returns an array of HistoryUser objects
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

//    public function findOneBySomeField($value): ?HistoryUser
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
