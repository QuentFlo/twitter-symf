<?php

namespace App\Controller;

use App\Entity\UserInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditProfileController extends AbstractController
{
    /**
     * @Route("/edit/profile", name="edit_profile")
     */
    public function index(Request $req): Response
    {
        $res = new Response();
        $res->setContent($req->request->get("profilePicture"));
        $em = $this->getDoctrine()->getManager();
        $userInfo = $this->getDoctrine()->getRepository(UserInfo::class);
        $userInfo = $userInfo->findById($this->getUser()->getId())[0];
        $userInfo->setAvatar($req->request->get("profilePicture"));
        $em->persist($userInfo);
        $em->flush();
        return $res;
    }
}
