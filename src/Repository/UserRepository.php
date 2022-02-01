<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
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
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getInfoFromId(int $userId) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("u, i.following, i.followers, i.avatar")
        ->from("App\Entity\User", "u")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "u.id = i.id")
        ->where("u.id LIKE :id")
        ->setParameter("id", $userId);

        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        // $em = $this->getEntityManager();
        // $query = $em->createQueryBuilder()
        // ->select("i")
        // ->from("App\Entity\UserInfo", "i")
        // ->where("i.id LIKE :id")
        // ->setParameter("id", $userId);

        // $query = $query->getQuery();
        
        // return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getMostPopularWithQuery(int $limit, string $myQuery) {
        $myQuery = "%" . $myQuery . "%";
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("u.id, u.username, u.description, u.joined, i.followers, i.avatar")
        ->from("App\Entity\User", "u")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "u.id = i.id")
        ->where("u.username LIKE :username")
        ->orderBy("i.followers", "DESC")
        ->setMaxResults( $limit )
        ->setParameter("username", $myQuery);

        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
