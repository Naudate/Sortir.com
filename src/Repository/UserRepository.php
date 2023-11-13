<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :identifier')
            ->orWhere('u.email = :identifier')
            ->setParameter('identifier', $usernameOrEmail)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByEmailOrUsername(string $pseudoOrEmail): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.pseudo = :identifier')
            ->orWhere('u.email = :identifier')
            ->setParameter('identifier', $pseudoOrEmail)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserWithPagination (int $page =1){

        $limit = 8;
        $req = $this->createQueryBuilder('u')
            ->setMaxResults($limit);

        $offset = $limit * ($page -1);
        $req->setFirstResult($offset);
        $query = $req->getQuery();



        $paginator = new Paginator($query, true);
        return $paginator;
    }

    public function GeneratePseudo(string $prenom){
        $randomString = '';
        $randomString.= substr($prenom, 0,3);
        //suppression des espaces qui pourrait exister
        $randomString = trim($randomString, "");
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < 7; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
