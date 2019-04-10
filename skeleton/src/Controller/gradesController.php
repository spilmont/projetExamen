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
use App\Form\GradeType;
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
     * @Route("admin/{class}/{idskill}/grade",name="create_grade")
     */
    public function Create(Request $request, $idskill, $class)
    {


        $reposkill = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $reposkill->find($idskill);
        $repouser = $this->getDoctrine()->getRepository(User::class);
        $users = $repouser->findBy(["class"=>$class]);
        $repograde = $this->getDoctrine()->getRepository(Grade::class);


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
        return $this->render("admin\creategrade.html.twig", ["users"=>$users,"skill" => $skill]);
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
            ->add('class',ChoiceType::class,["choices"=>["CP"=>"CP","CE1"=>"CE1","CE2"=>"CE2","CM1"=>"CM1","CM2"=>"CM2"]])
            ->add('skill',EntityType::class,[
                "class"=>Skill::class,
                'choice_label' => 'skill',

            ])
            ->add('save',SubmitType::class)
            ->getForm();



        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){

            $formskill = $form->get('skill')->getData();
            $formclass = $form->get('class')->getData();



            return $this->redirectToRoute("create_grade",['idskill'=>$formskill->getId(), 'class'=>"$formclass"]);

        }

        return $this->render('admin/choiceforgrade.html.twig',["class"=>$form->createView()]);



    }








}