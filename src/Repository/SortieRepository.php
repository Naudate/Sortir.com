<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Enum\Etat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }
    public function findBetweenDates(
        $dateDebut,
        $dateFin,
        $searchInput,
        $organisateurOnly,
        $user,
        $selectedSite,
    )
    {
        $qb = $this->createQueryBuilder('s');
        // Ajoutez des conditions pour la recherche par nom
        if ($searchInput) {
            $qb->andWhere('s.nom LIKE :searchInput')
                ->setParameter('searchInput', '%' . $searchInput . '%');
        }
        if ($dateDebut) {
            $qb->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $dateDebut);
        }
        if ($dateFin) {
            if ($qb->getDQLPart('where')) {
                $qb->andWhere('s.dateHeureFin <= :dateFin')
                    ->setParameter('dateFin', $dateFin . ' 23:59:59');
            } else {
                $qb->where('s.dateHeureFin <= :dateFin')
                    ->setParameter('dateFin', $dateFin . ' 23:59:59');
            }
        }
        if ($organisateurOnly && $user) {
            $qb->andWhere('s.organisateur = :user')
                ->setParameter('user', $user);
        }

        if ($selectedSite) {
            $qb->andWhere('s.site = :site')
                ->setParameter('site', $selectedSite);
        }

        $qb->andWhere('s.etat != :etat OR (s.etat = :etat AND s.organisateur = :user)')
            ->setParameter('etat', Etat::EN_CREATION)
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();

    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
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

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
