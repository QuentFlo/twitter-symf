<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\UserInfo;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DislikeController extends AbstractController
{

    private function addDislike(int $postId, ObjectRepository $post) : Post {
        $selectedPost = $post->find($postId);
        $selectedPost->setLikes($selectedPost->getLikes() - 1);
        return $selectedPost;
    }

    private function updateDb($row) {
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

    public function dislikePost(int $postId, UserInfo $user, array $likedPostArray, int $pos) : Response {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $post = $this->getDoctrine()->getRepository(Post::class);
        if ($this->updateDb($this->addDislike($postId, $post)) === True) {
            if (count($likedPostArray) === 1) {
                $this->updateDb($user->setLiked(NULL));
            } else {
                unset($likedPostArray[$pos]);
                $this->updateDb($user->setLiked(implode(";", $likedPostArray) . ";"));
            }
            $res->setContent(json_encode(array("data" => "post disliked")));
        } else {
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $res->setContent(json_encode(array("data" => "Internal Server Error")));
        }
        return $res;
    }

    public function dislike(string $likedPost, int $postId, Response $res, UserInfo $user) : Response {
        $likedPostArray = explode(";", $likedPost);
        array_pop($likedPostArray);
        $pos = array_search($postId, $likedPostArray);
        if ($pos === false) {
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);
            $res->setContent(json_encode(array("data" => "Cannot dislike a post that hasn't been liked")));
            return $res;
        } else {
            return $this->dislikePost($postId, $user, $likedPostArray, $pos);
        }
    }

    /**
     * @Route("/dislike/{postId}", name="dislike", methods="POST")
     */
    public function index(int $postId): Response
    {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $likedPost = $this->getDoctrine()->getRepository(UserInfo::class);
        $user = $likedPost->findById($this->getUser()->getId())[0];
        $likedPost = $user->getLiked();
        if ($likedPost === "NULL" || $likedPost === NULL) {
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);
            $res->setContent(json_encode(array("data" => "Cannot dislike a post that hasn't been liked")));
            return $res;
         } else {
             return $this->dislike($likedPost, $postId, $res, $user);
         }
    }
}
