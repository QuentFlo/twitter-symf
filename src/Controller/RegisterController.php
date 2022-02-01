<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\User;
use App\Entity\UserInfo;
use App\Form\UsersType;
use Attribute;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $attributes;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->attributes = ["username", "location", "description", "password"];
    }

    public function checkValues(Request $req) {
        foreach ($this->attributes as $attribute) {
            if ($req->request->get($attribute) === NULL) {
                return FALSE;
            }
        }
        return True;
    }

    public function fillAttributes(Request $req, User $user) : User
    {
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $req->request->get("password")
            ));
            $user->setUsername($req->request->get("username"));
            $user->setLocation($req->request->get("location"));
            $user->setDescription($req->request->get("description"));
            return $user;
    }

    public function fillUserInfo(int $userId, $em) {
        $userInfo = new UserInfo();
        $userInfo->setId($userId);
        $userInfo->setFollowed("$userId;");
        $userInfo->setFollowers(0);
        $userInfo->setFollowing(0);
        $em->persist($userInfo);
        $em->flush();
    }


    public function register(Request $req, User $user) : Response
    {
        $res = new Response();
        if ($this->checkValues($req) === False) {
            $res->setContent(json_encode(array("data" => "An field was empty")));
            $res->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            return $res;
        }
        try {
            $user = $this->fillAttributes($req, $user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->fillUserInfo($user->getId(), $em);
            // $res->setContent(json_encode(array("data" => $user->getId())));
            return new RedirectResponse("/login");
        } catch (Exception $e) {
            return $this->render('security/signup.html.twig', ['e' => true]);
        }
        return $res;
    }

    /**
     * @Route("/register", name="register")
     */
    public function index(Request $req): Response
    {
        $user = new User();
        $user->setJoined(new DateTime());
        return $this->register($req, $user);
    }
}
