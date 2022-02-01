<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\UserInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    public function formatPost(&$post) {
        $hashtag = new HashtagController();
        $post[0]["username"] = $post["username"];
        $post[0]["avatar"] = $post["avatar"];
        $post = $post[0];
        if ($post["avatar"] === null) {
            $post["avatar"] = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
        }
        $post["date"] = $post["date"]->format("d/m(H:i)");
        $post["content"] = $hashtag->convertHashtag($post["content"]);
    }

    /**
     * @Route("/home", name="home", methods={"GET"})
     */
    public function index(Request $req): Response
    {
        $userInfo = $this->getDoctrine()->getRepository(UserInfo::class);
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $followed = $userInfo->findById($this->getUser()->getId())[0];
        $avatar = $followed->getAvatar();
        if ($avatar === null) {
            $avatar = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
        }
        $followed = $followed->getFollowed();
        if ($followed === NULL) {
            $followedPost = array("error" => "You're not following any user register and follow some to see tweets");
        } else {
            $followedUserArray = explode(";", $followed);
            $followedPost = $postRepository->getByAuthorsIds($followedUserArray);
            foreach ($followedPost as &$post) {
                $this->formatPost($post);
            }
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'Tweets' => $followedPost,
            'title' => "Home",
            "avatar" => $avatar,
            "oldlocation" => $req->headers->get('referer'),
            "userId" => $this->getUser()->getId(),
            "username" => $this->getUser()->getUsername()
        ]);
    }
}
