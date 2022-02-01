<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class TweetController extends AbstractController
{
    /**
     * @Route("/post/{id}", name="tweet")
     */

     public function index(Request $req, $id): Response
    {
        $em = $this->getDoctrine()->getRepository(Post::class);
        $post = $em->findByIdToArray($id)[0];
        $replys = $em->findReplies($id);
        $hashtag = new HashtagController();
        $post[0]["avatar"] = $post["avatar"];
        $post = $post[0];
        if ($post["avatar"] === null) {
            $post["avatar"] = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
        }
        $post["date"] = $post["date"]->format("d/m(H:i)");
        $post["content"] = $hashtag->convertHashtag($post["content"]);
        dump($replys);
        foreach($replys as &$reply) {
            $reply[0]["avatar"] = $reply["avatar"];
            $reply[0]["username"] = $reply["username"];
            $reply = $reply[0];
            if ($reply["avatar"] === null) {
                $reply["avatar"] = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
            }
            $reply["date"] = $reply["date"]->format("d/m(H:i)");
            $reply["content"] = $hashtag->convertHashtag($reply["content"]);
        }
        dump($replys);

        return $this->render('tweet/index.html.twig', [
            'controller_name' => 'TweetController',
            'pageID' => $id,
            'post' => $post,
            'Replys' => $replys,
            "title" => "Tweet $id",
            "oldlocation" => $req->headers->get('referer'),
            "username" => $this->getUser()->getId(),
            "userId" => $this->getUser()->getId()
        ]);
    }

    
}
