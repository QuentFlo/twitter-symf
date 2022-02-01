<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UsersType;
use App\Entity\Images;
use App\Entity\Post;
use DateTime;

class ProfileController extends AbstractController
{
    private $file = "8d24085380db3fcfd6f5286cb90306b0.png";
/*    private $cookie ;
    private $res;*/

    public function retrieveUserInfo(int $id) {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $userInfo = $userRepo->getInfoFromId($id)[0];
        $userInfo[0]["following"] = $userInfo["following"];
        $userInfo[0]["followers"] = $userInfo["followers"];
        $userInfo[0]["avatar"] = $userInfo["avatar"];
        $userInfo = $userInfo[0];
        if ($userInfo["avatar"] === null) {
            $userInfo["avatar"] = "https://cdn.icon-icons.com/icons2/1736/PNG/512/4043260-avatar-male-man-portrait_113269.png";
        }
        $userInfo["joined"] = $userInfo["joined"]->format("d m y");
        return $userInfo;
    }


    public function retrieveUserPost(int $id) {
        $post = $this->getDoctrine()->getRepository(Post::class);
        $posts = $post->getByAuthorId($id);
        dump($posts);
        foreach($posts as &$post) {
            $post[0]["avatar"] = $post["avatar"];
            $post = $post[0];
            $post["date"] = $post["date"]->format("d/m(H:i)");
            $post["username"] = $this->getUser()->getUsername();
            $hashtag = new HashtagController();
            $post["content"] = $hashtag->convertHashtag($post["content"]);
        }
        return $posts;
    }

    public function returnProfilePage(Request $req, $userInfo, $posts, $user, $form) {
        dump($posts);
        return $this->render('profile/index.html.twig', [
            'username' => $userInfo["username"],
            'location' =>$userInfo["location"],
            'description' => $userInfo["description"],
            'joined' => $userInfo["joined"],
            'following'=> $userInfo["following"],
            'followers'=> $userInfo["followers"],
            "id" => $userInfo["id"],
            // "avatar" => "navi",
            "avatar" => $userInfo["avatar"],
            "Tweets" => $posts,
            "title" => "Profil",
            "oldlocation" => $req->headers->get('referer'),
            "userId" => $this->getUser()->getId(),
            'myForm' => $form->createView(),
            'user' => $user,
            'filepath' =>$this->file,
        ]);
    }

    /**
     * @Route("/profile/{id}", name="profile other user", methods={"GET"})
     */
    public function profileAnotherUser(Request $req, int $id): Response {
        $userInfo = $this->retrieveUserInfo($id);
        $posts = $this->retrieveUserPost($id);
        $user = $this->getUser();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($req);
        return $this->returnProfilePage($req, $userInfo, $posts, $user, $form);
    }


    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @return Response
     */
    public function index(Request $req): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('images')->getData();

            foreach($images as $image){
                $this->file = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $this->file
                );
            }
           /* $this->cookie = Cookie::create('picture')
                ->withValue($this->file)
                ->withExpires(new \DateTime('+1 year'))
                ->withSecure(true)->withHttpOnly(true);
            $this->res = new Response();
            $this->res->headers->setCookie($this->cookie);
            $this->res->send();
            dump($this->res->headers->getCookies());
            dump($this->cookie->getValue());*/
        }

        $id = $this->getUser()->getId();
        $userInfo = $this->retrieveUserInfo($id);
        $posts = $this->retrieveUserPost($id);
        return $this->returnProfilePage($req, $userInfo, $posts,$user,$form);
    }

    /**
     * @Route("/delimg/{id}", name="del_img", methods={"DELETE"})
     *
     */

    /*public function deleteImage(Images $image, Request $request): JsonResponse
    {
        $data = $request->getContent();
        if($this->isCsrfTokenValid('delete'. $image->getId(), $data['_token'])){
            $name = $image->getName();
            unlink($this->getParameter('images_directory'). '/' .$name);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return new JsonResponse(['Success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Invalid Token'],400);
        }
    }*/

   
}