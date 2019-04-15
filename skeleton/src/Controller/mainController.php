<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 14/02/2019
 * Time: 19:50
 */

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Skill;
use App\Entity\User;
use App\Entity\Grade;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function homepage(Request $request, AuthenticationUtils $authenticationUtils)
    {
        //   return the homepage view
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();


        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }
        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('user', ['lastname' => $this->getUser()->getLastname(), 'firstname' => $this->getUser()->getFirstname()]);
        } else {
            return $this->render('home.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,

            ]);
        }


    }


    /**
     * @Route("/user/{lastname}/{firstname}",name="user")
     * */
    public function user(Request $request, User $user)
    {
        $reposkill = $this->getDoctrine()->getRepository(Skill::class);
        $skills = $reposkill->findAll();
        $repograde = $this->getDoctrine()->getRepository(Grade::class);
        $repocomment = $this->getDoctrine()->getRepository(Comments::class);
        $receiver = $repocomment->findAll();
        $sender = $repocomment->findBy(['sender'=>$user->getId() ]);
        $lastname = $user->getLastname();
        $firstname = $user->getFirstname();
        $usercode  = $user->getUsercode();
        $userid = $user->getId();
        $grades = $repograde->findBy(["user" => $user->getId(), "skill" => $skills]);

        dump($sender);

        $pp = [];
        foreach ($grades as $grade) {

            $pp[] = ["skill" => $grade->getskill()->getskill(), "grade" => $grade->getgrades()];

        }

        $objgrade = json_encode($pp);

        return $this->render('user.html.twig',
            [
                'usercode'=>$usercode,
                'nom' => $lastname,
                "prenom" => $firstname,
                "skills" => $skills,
                'grades' => $grades,
                "objgrade" => $objgrade,
                "receivers"=>$receiver,
                "senders"=>$sender,
                "userid"=>$userid]);


    }


}