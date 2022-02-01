<?php

namespace App\Controller;

use App\Entity\UserInfo;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FollowController extends AbstractController
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


    public function addFollower(int $userId) {
        $em = $this->getDoctrine()->getRepository(UserInfo::class);
        $user = $em->findById($userId)[0];
        $user->setFollowers($user->getFollowers() + 1);
        $this->updateDb($user);

    }

    public function followUser(int $userId, UserInfo $followed, string $followedUsers) : Response
    {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $followedUsers = $followedUsers . "$userId;";
        $this->addFollower($userId);
        $followed->setFollowing($followed->getFollowing() + 1);
        $this->updateDb($followed->setFollowed($followedUsers));
        $res->setContent(json_encode(array("data" => "user followed")));
        return $res;
    }

    public function follow(string $followedUsers, int $userId, UserInfo $followed) : Response
    {
        $followedUsersArray = explode(";", $followedUsers);
        array_pop($followedUsersArray);
        $pos = array_search($userId, $followedUsersArray);
        if ($pos === false) {
            return $this->followUser($userId, $followed, $followedUsers);
        } else {
            $res = new Response();
            $res->headers->set('Content-Type', 'application/json');
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);    
            $res->setContent(json_encode(array("data" => "You already follow this user")));
            return $res;
        }
    }

    /**
     * @Route("/follow/{userId}", name="follow", methods={"POST"})
     */
    public function index(int $userId): Response
    {
        $followedUsers = $this->getDoctrine()->getRepository(UserInfo::class);
        $followed = $followedUsers->findById($this->getUser()->getId())[0];
        $followedUsers = $followed->getFollowed();
        if ($followedUsers === "NULL" || $followedUsers === NULL) {
            return $this->followUser($userId, $followed, "");
        } else {
            return $this->follow($followedUsers, $userId, $followed);
        }
    }
}
