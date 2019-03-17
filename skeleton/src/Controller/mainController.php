<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 14/02/2019
 * Time: 19:50
 */

namespace App\Controller;

use App\Entity\Skill;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

//main controller for show the different page of website

class mainController extends securityController
{
    /**
     * @return mixed
     * @Route("/",name="homepage")
     */
    public function homepage( Request $request,AuthenticationUtils $authenticationUtils)
    {
        //   return the homepage view
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();



        if(true===$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('admin');
        }
        if(true===$this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return $this->redirectToRoute('user',['lastname'=>$this->getUser()->getLastname(), 'firstname'=> $this->getUser()->getFirstname()]);
        }else{return $this->render('home.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error,

        ]);
        }


    }



    /**
     * @Route("/user/{lastname}/{firstname}",name="user")
     * */
    public function user(Request $request,User $user)
    {
        $em = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $em->findAll();
        $lastname = $user->getLastname();
        $firstname = $user->getFirstname();


        return $this->render('user.html.twig',['nom'=>$lastname, "prenom"=>$firstname,"skills"=>$skill]);
    }





}