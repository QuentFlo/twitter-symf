<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HashtagController extends AbstractController
{


    public function convertHashtag(string &$string) {
        if (strstr($string, "#") !== False) {
            $hashtag = explode(" ", explode("#", $string)[1])[0];
            $string = str_replace("#$hashtag", "<a href='/posts/$hashtag'>#$hashtag</a>", $string);
        }
        return $string;
    }

         /**
     * @Route("/posts/{hashtag}", name="posts", methods={"GET"})
     */
    public function hastag(Request $req,  string $hashtag): Response
    {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $post = $this->getDoctrine()->getRepository(Post::class);
        $hashtag_post = $post->getByHashtag($hashtag);
        dump($hashtag_post);
        foreach ($hashtag_post as &$post) {
            $post[0]["avatar"] = $post["avatar"];
            $post = $post[0];
            $post["date"] = $post["date"]->format("d/m(H:i)");   
            $post["content"] = $this->convertHashtag($post["content"]);
        }


        dump($hashtag_post);
        return $this->render('hashtag/index.html.twig', [
            'controller_name' => 'HomeController',
            'Tweets' => $hashtag_post,
            'title' => "#$hashtag",
            "oldlocation" => $req->headers->get('referer'),
            "username" => $this->getUser()->getUsername(),
            "userId" => $this->getUser()->getId()
        ]);
    }
}
