<?php

namespace App\Controller;

use App\Entity\Post;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RetweetController extends AbstractController
{

    public function retweet(int $postId, int $retweeterId, Post $OGPost): Post
    {
        dump($retweeterId);
        $retweet = new Post();
        $retweet->setContent($OGPost->getContent());
        $retweet->setAuthor($OGPost->getAuthor());
        $retweet->setDate(new DateTime(null, new DateTimeZone('Europe/Paris')));
        $retweet->setAuthorId($retweeterId);
        $retweet->setLikes(0);
        $retweet->setReplyTo(NULL);
        $retweet->setRetweet($postId);
        $retweet->setNbrReplyTo(0);
        $retweet->setNbrRetweet(0);
        return $retweet;
    }

    public function addRetweet(Post $post, Response $res, $retweetId) : Response
    {
        $post->setNbrRetweet($post->getNbrRetweet() + 1);
        if ($this->updateDb($post) !== False) {
            $res->setStatusCode(Response::HTTP_CREATED);
            $res->setContent(json_encode(array("id" => $retweetId)));
        } else {
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $res->setContent(json_encode(array("data" => "Internal Server Error")));
        }
        return $res;
    }

    private function updateDb($row) : bool
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

    /**
     * @Route("/retweet/{postId}", name="retweet", methods={"POST"})
     */
    public function index(int $postId): Response
    {
        $res = new Response();
        $res->headers->set("Content-Type", "application/json");
        $em = $this->getDoctrine()->getRepository(Post::class);
        if (count($em->findRetweet($postId, $this->getUser()->getId())) !== 0) {
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);
            $res->setContent(json_encode(array("data" => "post already retweeted")));
            return $res;
        }
        $retweet = $this->retweet($postId, $this->getUser()->getId(), $em->find($postId));
        if ($this->updateDb($retweet) === False) {
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $res->setContent(json_encode(array("data" => "Internal Server Error")));
        } else {
            return $this->addRetweet($em->find($postId), $res, $retweet->getId());
        }
        return $res;
    }
}
