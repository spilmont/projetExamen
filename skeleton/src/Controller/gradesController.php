<?php
/**
 * Created by PhpStorm.
 * User: Administrateur
 * Date: 21/03/2019
 * Time: 10:16
 */

namespace App\Controller;


use App\Entity\Grade;
use App\Entity\Skill;
use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Tests\Compiler\G;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class gradesController extends AbstractController
{
    /**
     * @Route("admin/{class}/{idskill}/gradetoclass",name="create_grade_class")
     */
    public function Createtoclass(Request $request, $idskill, $class)
    {


        $reposkill = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $reposkill->find($idskill);
        $repouser = $this->getDoctrine()->getRepository(User::class);
        $users = $repouser->findBy(["class"=>$class]);



        if( !empty($_POST)){

        foreach ($users as $user){
            $grades = new Grade();

            $formgrade = filter_var($_POST["grade".$user->getId()],FILTER_SANITIZE_NUMBER_INT);

        $grades->setSkill($skill);
        $grades->setUser($user);
        $grades->setGrades($formgrade);
            $em = $this->getDoctrine()->getManager();
            $em->persist($grades);
            $em->flush();


        }
    }
        return $this->render("admin\creategradetoclass.html.twig", ["users"=>$users,"skill" => $skill]);
    }


    /**
     * @Route("admin/{iduser}/gradetouser",name="create_grade_user")
     */
    public function Createtouser(Request $request, $iduser){

        $repouser = $this->getDoctrine()->getRepository(User::class);
        $user = $repouser->find($iduser);
        $reposkill = $this->getDoctrine()->getRepository(skill::class);
        $skills = $reposkill->findAll();

        if( !empty($_POST)){

            foreach ($skills as $skill){
                $grades = new Grade();

                $formgrade = filter_var($_POST["grade".$skill->getId()],FILTER_SANITIZE_NUMBER_INT);

                $grades->setSkill($skill);
                $grades->setUser($user);
                $grades->setGrades($formgrade);
                $em = $this->getDoctrine()->getManager();
                $em->persist($grades);
                $em->flush();


            }
        }




        return $this->render("admin\creategradetouser.html.twig",['users'=>$user,'skills'=>$skills]);

    }




    /**
     * @Route("/admin/choicegrade",name="choice_grade")
     */
    public function choice(Request $request){

        $reposkill = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $reposkill->findAll();
        $repouser = $this->getDoctrine()->getRepository(User::class);
        $user = $repouser->findAll();


        $form = $this->createFormBuilder($user)
            ->add('skill',EntityType::class,[
                "class"=>Skill::class,
                'choice_label' => 'skill',

            ])
            ->add('save',SubmitType::class)
            ->getForm();



        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){

            $user->setClass($this->getUser()->getClass());
            $formskill = $form->get('skill')->getData();
            $formclass = $form->get('class')->getData();



            return $this->redirectToRoute("create_grade_class",['idskill'=>$formskill->getId(), 'class'=>"$formclass"]);

        }

        return $this->render('admin/choiceforgrade.html.twig',["class"=>$form->createView()]);



    }








}