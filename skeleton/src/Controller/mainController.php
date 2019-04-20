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
use Doctrine\ORM\Query\Expr\Select;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_SUPERADMIN')) {
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
         $com = new Comments();

        $reposkill = $this->getDoctrine()->getRepository(Skill::class);
        $users = $this->getDoctrine()->getRepository(User::class)->findOneBy(["id"=>$this->getUser()->getIdProf()]);
        $skills = $reposkill->findAll();
        $repograde = $this->getDoctrine()->getRepository(Grade::class);
        $repocomment = $this->getDoctrine()->getRepository(Comments::class);
        $comments= $repocomment->findBy([],['id'=>'DESC']);
        $lastname = $user->getLastname();
        $firstname = $user->getFirstname();
        $usercode  = $user->getUsercode();
        $userid = $user->getId();
        $grades = $repograde->findBy(["user" => $user->getId(), "skill" => $skills]);
        $em = $this->getDoctrine()->getManager();
        $query= $em->createQueryBuilder()->select("s.skill","avg(g.grades)")->from(Grade::class,'g')->join("g.skill","s")->where("g.user = :studient")->groupby('g.skill')->setparameter('studient',$this->getUser()->getid())->getquery();
        $gradus = $query->getResult();


        $form = $this->createFormBuilder($com)
            ->add('comment',TextareaType::class)
            ->add('save',SubmitType::class)
            ->getForm();

       $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $com->setReceiver($users);
            //dd($com);
            $com->setSender($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($com);
            $em->flush();

            return  $this->redirectToRoute('user',[ 'lastname' => $lastname,
                "firstname" => $firstname,]);

     }

        $objgrade = json_encode($gradus);

        return $this->render('user.html.twig',
            [
                'gradus'=>$gradus,
                'usercode'=>$usercode,
                'nom' => $lastname,
                "prenom" => $firstname,
                "skills" => $skills,
                'grades' => $grades,
                "objgrade" => $objgrade,
                "comments"=>$comments,
                "formCom"=>$form->createView(),
                "userid"=>$userid]);


    }

    /**
     * @Route("/user/totalgrades/{selectskill}/{lastname}/{firstname}",name="total_grades")
     */
    public function totalgrade(User $user, $selectskill ){



        $reposkill = $this->getDoctrine()->getRepository(Skill::class);
        $repograde = $this->getDoctrine()->getRepository(Grade::class);
        $skills = $reposkill->findOneBy(["skill"=>$selectskill]);
        $grades = $repograde->findBy([ "skill" => $skills,"user"=>$user]);


        $lastname = $user->getLastname();
        $firstname = $user->getFirstname();


        $gradeskills = [];
        $totalgrades = [];
        foreach ($grades as $grade){

            $gradeskills []=$grade->getskill()->getskill();
            $totalgrades []=$grade->getgrades();

        }







        return $this->json(["skills"=>$gradeskills,"grades"=>$totalgrades],200);


    }



}