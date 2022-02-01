<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\UserInfo;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UnfollowController extends AbstractController
{

    private function updateDb($row)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($row);
        try {
            $em->flush();
            return True;
        } catch (Exception $err) {
            var_dump($err->getMessage());
            return False;
        }
    }

    public function unfollowUser(UserInfo $followed, array $followedUsersArray, int $pos): Response
    {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        if (count($followedUsersArray) === 1) {
            $this->updateDb($followed->setFollowed(NULL));
        } else {
            unset($followedUsersArray[$pos]);
            $this->updateDb($followed->setFollowed(implode(";", $followedUsersArray) . ";"));
        }
        $followed->setFollowing($followed->getFollowing() - 1);
        $res->setContent(json_encode(array("data" => "User Unfollowed")));
        return $res;
    }

    public function removeFollower(int $userId) {
        $em = $this->getDoctrine()->getRepository(UserInfo::class);
        $user = $em->findById($userId)[0];
        $user->setFollowers($user->getFollowers() - 1);
        $this->updateDb($user);
    }

    public function unfollow(string $followedUsers, int $userId, Response $res, UserInfo $followed): Response
    {
        $followedUsersArray = explode(";", $followedUsers);
        array_pop($followedUsersArray);
        $pos = array_search($userId, $followedUsersArray);
        if ($pos === false) {
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);
            $res->setContent(json_encode(array("data" => "You cannot unfollow a user you aren't following")));
            return $res;
        } else {
            $this->removeFollower($userId);
            return $this->unfollowUser($followed, $followedUsersArray, $pos);
        }
    }

    /**
     * @Route("/unfollow/{userId}", name="unfollow", methods={"POST"})
     */
    public function index(int $userId): Response
    {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $followedUsers = $this->getDoctrine()->getRepository(UserInfo::class);
        $followed = $followedUsers->findById($this->getUser()->getId())[0];
        $followedUsers = $followed->getFollowed();
        if ($followedUsers === "NULL" || $followedUsers === NULL) {
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);
            $res->setContent(json_encode(array("data" => "Cannot dislike a post that hasn't been Followed")));
            return $res;
        } else {
            return $this->unfollow($followedUsers, $userId, $res, $followed);
        }
    }
}
