<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 22/02/2019
 * Time: 12:34
 */

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Skill;
use Symfony\Component\Routing\Annotation\Route;
// controller for CRUD For  skill panel

class skillController extends AbstractController
{
    /**
     * @Route("admin/create/skill", name="create_skill")
     */
    public function create(Request $request){

        // create new skill with from

        // create new instance of skill object

        $skill = new Skill();


        // create forbuilder for skill

        $form = $this->createFormBuilder($skill)
            ->add('skill', TextType::class,['label'=>false,"attr"=>['placeholder'=>"competence"]])
            ->add('save', SubmitType::class,['label'=>'ajouter la compétences'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()and $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('show_skill');
        }
        return $this->render('admin/createskill.html.twig',['skill'=>$form->createView()
        ]);

    }

    /**
     * @Route("admin/skill", name="show_skill")
     */
    public function show(){

        // return the skill panel

        $repository = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $repository->findAll();

        return $this->render("admin/skill.html.twig", ["skill" => $skill,]);


    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("admin/skill/delete/{id}", name="delete_skill")
     */
    public function delete($id){

        // delete skill by admin
        $em = $this->getDoctrine()->getManager();

        $skill = $em->getRepository(Skill::class)->find($id);

        $em->remove($skill);
        $em->flush();

        return $this->redirectToRoute('show_skill', ['id' => $skill->getId()]);
    }

    /**
     * @Route("admin/skill/update/{id}", name="update_skill")
     */
    public function update(Request $request, $id){

        $em = $this->getDoctrine()->getManager();

        $skill = $em->getRepository(Skill::class)->find($id);

        $form = $this->createFormBuilder($skill)
            ->add('skill', TextType::class,['label'=>'compétence'])
            ->add('save', SubmitType::class,['label'=>'ajouter la compétences'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()and $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('show_skill');
        }

         return $this->render('admin/updateskill.html.twig',['update'=>$form->createView()
         ]);
    }



}