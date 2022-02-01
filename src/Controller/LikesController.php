<?php

namespace App\Controller;

use App\Entity\UserInfo;
use App\Entity\Post;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController
{

    private function addLike(int $postId, ObjectRepository $post): Post
    {
        $selectedPost = $post->find($postId);
        $selectedPost->setLikes($selectedPost->getLikes() + 1);
        return $selectedPost;
    }

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


    public function likePost(int $postId, UserInfo $user, string $likedPost) : Response
    {
        $res = new Response();
        $res->headers->set('Content-Type', 'application/json');
        $post = $this->getDoctrine()->getRepository(Post::class);
        if ($this->updateDb($this->addLike($postId, $post)) === True) {
            $likedPost = $likedPost . "$postId;";
            $this->updateDb($user->setLiked($likedPost));
            $res->setContent(json_encode(array("data" => "post liked")));
        } else {
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $res->setContent(json_encode(array("data" => "Internal Server Error")));
        }
        return $res;
    }

    public function like(string $likedPost, int $postId, UserInfo $user) : Response
    {
        $likedPostArray = explode(";", $likedPost);
        array_pop($likedPostArray);
        $pos = array_search($postId, $likedPostArray);
        if ($pos === false) {
            return $this->likePost($postId, $user, $likedPost);
        } else {
            $res = new Response();
            $res->headers->set('Content-Type', 'application/json');
            $res->setStatusCode(Response::HTTP_BAD_REQUEST);    
            $res->setContent(json_encode(array("data" => "This post is already liked")));
            return $res;
        }
    }

    /**
     * @Route("/like/{postId}", name="likes", methods={"POST"})
     */
    public function index(int $postId): Response
    {
        $likedPost = $this->getDoctrine()->getRepository(UserInfo::class);
        $user = $likedPost->findById($this->getUser()->getId())[0];
        $likedPost = $user->getLiked();
        if ($likedPost === "NULL" || $likedPost === NULL) {
            return $this->likePost($postId, $user, "");
        } else {
            return $this->like($likedPost, $postId, $user);
        }
    }
}
