<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getByAuthorId(int $authorId) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("p, i.avatar")
        ->from("App\Entity\Post", "p")
        ->innerJoin("App\Entity\User", "u", "WITH", "u.id = p.authorId")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->where("p.authorId = :authorId")
        ->orderBy('p.date', 'DESC')
        ->setMaxResults( 50 )
        ->setParameter("authorId", $authorId);


        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getByAuthorsIds(array $authorsIds) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("p, u.username, i.avatar")
        ->from("App\Entity\Post", "p")
        ->innerJoin("App\Entity\User", "u", "WITH", "u.id = p.authorId")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->where("p.authorId in (:authorId)")
        ->orderBy('p.date', 'DESC')
        ->setMaxResults( 50 )
        ->setParameter("authorId", $authorsIds);

        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getByHashtag(string $hashtag) {
        $em = $this->getEntityManager();
        $hashtag = "%#" . $hashtag ."%";
        $query = $em->createQueryBuilder()
        ->select("p, i.avatar")
        ->from("App\Entity\Post", "p")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->where("p.content LIKE :hashtag")
        ->orderBy("p.date", "DESC")
        ->setParameter("hashtag", $hashtag);

        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function findRetweet(int $retweet, int $authorId) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("p, i.avatar")
        ->from("App\Entity\Post", "p")
        ->where("p.authorId LIKE :authorId")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->andWhere("p.retweet LIKE :retweet")
        ->setParameter("authorId", $authorId)
        ->setParameter("retweet", $retweet);

        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function findByIdToArray(int $id) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("p, i.avatar")
        ->from("App\Entity\Post", "p")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->where("p.id LIKE :id")
        ->setParameter("id", $id);
        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function findReplies(int $id) {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("p, i.avatar, u.username")
        ->from("App\Entity\Post", "p")
        ->innerJoin("App\Entity\User", "u", "WITH", "u.id = p.authorId")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->where("p.replyTo LIKE :id")
        ->setParameter("id", $id);
        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function findMostLikedPostWithQuery(int $limit, string $myQuery) {
        //SELECT * FROM post WHERE content LIKE "%test%" ORDER BY (likes) DESC LIMIT 5
        $myQuery = "%" . $myQuery . "%";
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
        ->select("p, u.username, i.avatar")
        ->from("App\Entity\Post", "p")
        ->where("p.content LIKE :query")
        ->innerJoin("App\Entity\User", "u", "WITH", "u.id = p.authorId")
        ->innerJoin("App\Entity\UserInfo", "i", "WITH", "i.id = p.authorId")
        ->orderBy('p.likes', 'DESC, p.date')
        ->setMaxResults($limit)
        ->setParameter("query", $myQuery);

        $query = $query->getQuery();
        
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

}
