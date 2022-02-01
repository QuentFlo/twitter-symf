<?php

namespace App\Controller;

use App\Entity\Post;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReplyController extends AbstractController
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

    public function addReplyNumber(int $postId) {
        $em = $this->getDoctrine()->getRepository(Post::class);
        $post = $em->findById($postId)[0];
        $post = $post->setNbrReplyTo($post->getNbrReplyTo() + 1);
        $this->updateDb($post);
    }

    /**
     * @Route("/reply/{postId}", name="reply", methods={"POST"})
     */
    public function index(Request $req, int $postId): Response
    {
        $post = new Post();
        $res = new Response();
        $postController = new PostController();
        $res->headers->set('Content-Type', 'application/json');
        if ($postController->checkAttributes($req) === False) {
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);
            $res->setContent(json_encode(array("data" => "attributes are missing")));
            return $res;
        }
        $post = $postController->fillAttributes($req, $post);
        $post->setReplyTo($postId);
        if ($this->updateDb($post) === True) {
            $this->addReplyNumber($postId);
            $res->setContent(json_encode(array("data" => $post->getId())));
        } else {
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $res->setContent(json_encode(array("data" => "Internal Server Error")));
        }
        return $res;
    }
}
