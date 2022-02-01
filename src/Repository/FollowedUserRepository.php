<?php

namespace App\Repository;

use App\Entity\FollowedUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FollowedUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method FollowedUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method FollowedUser[]    findAll()
 * @method FollowedUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowedUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FollowedUser::class);
    }

    // /**
    //  * @return FollowedUser[] Returns an array of FollowedUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FollowedUser
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
