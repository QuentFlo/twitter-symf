<?php

namespace App\Controller;

use App\Entity\Post;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{


    /**
     * @Route("/post", name="post", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }


    public function fillAttributes(Request $req, Post $post) : Post {
        $post->setAuthor($req->request->get("author"));
        $post->setContent($req->request->get("content"));
        $post->setAuthorId($req->request->get("authorId"));
        $post->setLikes(0);
        $post->setDate(new DateTime(null, new DateTimeZone('Europe/Paris')));
        $post->setReplyTo(NULL);
        $post->setRetweet(NULL);
        $post->setNbrReplyTo(0);
        $post->setNbrRetweet(0);
        return $post;
    }

    public function checkAttributes(Request $req) : Bool {
        if ($req->request->get("author") === null || $req->request->get("content") === null || $req->request->get("authorId") === null) {
            return False;
        }
        return True;
    }

    public function createPost(Request $req) {
        $post = new Post();
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        if ($this->checkAttributes($req) === False) {
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $res->setContent(json_encode(array("data" => "attributes are missing")));
            return $res;
        }
        $post = $this->fillAttributes($req, $post);
        return $post;
    }

    /**
     * @Route("/post", name="post", methods={"POST"})
     */    
    public function addPost(Request $req) {
        $post = $this->createPost($req);
        if (get_class($post) !== "App\Entity\Post") {
            return $post;
        }
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        // $res->setContent(json_encode(array("id" => $post->getId())))
        // return $res;
        return $this->redirectToRoute("home");
    }
}
