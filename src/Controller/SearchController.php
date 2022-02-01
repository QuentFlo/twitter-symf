<?php

namespace App\Controller;

use App\Entity\UserInfo;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $req): Response
    {
        $query = $req->query->get('search');
        $posts = $this->getPosts($query);
        $users = $this->getUsers($query);
        dump($users);
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            "Tweets" => $posts,
            "Users" => $users,
            'title' => "Search",
            "oldlocation" => $req->headers->get('referer'),
            "userId" => $this->getUser()->getId(),
            "username" => $this->getUser()->getUsername()
        ]);
    }

    public function getPosts($query) {
        $em = $this->getDoctrine()->getRepository(Post::class);
        $posts = $em->findMostLikedPostWithQuery(5, $query);
        $hashtag = new HashtagController();
        foreach ($posts as &$post) {
            $post[0]["username"] = $post["username"];
            $post[0]["avatar"] = $post["avatar"];
            $post = $post[0];
            if ($post["avatar"] === null) {
                $post["avatar"] = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
            }
            $post["date"] = $post["date"]->format("d/m(H:i)");
            $post["content"] = $hashtag->convertHashtag($post["content"]);
        }
        return $posts;
    }

    public function getUsers(string $query) {
        $em = $this->getDoctrine()->getRepository(User::class);
        $following = $this->getDoctrine()->getRepository(UserInfo::class);
        $users = $em->getMostPopularWithQuery(5, $query);
        $following = $following->findById($this->getUser()->getId())[0];
        $following = $following->getFollowed();
        $followingArray = explode(";", $following);
        foreach ($users as &$user) {
            $user["joined"] = $user["joined"]->format("d/m Y");
            if (array_search($user["id"], $followingArray) === false) {
                $user["following"] = false;
            } else {
                $user["following"] = true;
            }
            if ($user["avatar"] === null) {
                $user["avatar"] = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
            }
        }
        return $users;
    }

}