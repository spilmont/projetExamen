<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 17/02/2019
 * Time: 20:22
 */

// controller for create a new studient use object user
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use App\Entity\User;

// controller for CRUD For  administrator panel

class adminController extends AbstractController
{


    /**
     * @Route("/createuserbyadmin", name="user_by_admin")
     */
    public function create(Request $request)
    {
        //create a new studient with the form

        // create new instance of user object
        $user = new User();

        // create formbuilder for enter information of studient
        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class, ['label' => 'nom : '])
            ->add('firstname', TextType::class, ['label' => 'prénom : '])
            ->add('password', PasswordType::class, ['label' => 'mot de passe : '])
            ->add('usercode', TextType::class, ['label' => 'code élève : '])
            ->add('idrank', IntegerType::class)
            ->add('save', SubmitType::class)
            ->getForm();



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('admin');

        }


        return $this->render('createuseradmin.html.twig', [
            'user' => $form->createView(),
        ]);

    }

    /**
     * @Route("/admin",name="admin")
     * */
    public function show()
    {
        // return the administrator panel

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findAll();
        return $this->render("admin.html.twig", ["user" => $user,]);


    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("admin/delete/{id}",name="remove_user")
     * */

    public function delete($id)
    {
        // for delete user by admin


        // use entity manager
        $em = $this->getDoctrine()->getManager();

        // entity manager use repository of user class and search user by id
        $user = $em->getRepository(User::class)->find($id);

        // remove  user
        $em->remove($user);
        //save ans send action on bdd
        $em->flush();

        //return administrator panel
        return $this->redirectToRoute('admin', ['id' => $user->getId()]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("admin/update/{id}",name="update_user")
     * */
    public function update(Request $request, $id)
    {
        // for update user by admin



        // use entity manager
        $em = $this->getDoctrine()->getManager();

        // entity manager use repository of user class and search user by id
        $user = $em->getRepository(User::class)->find($id);


        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class, ['label' => 'nom : '])
            ->add('firstname', TextType::class, ['label' => 'prénom : '])
            ->add('password', TextType::class, ['label' => 'mot de passe : '])
            ->add('usercode', TextType::class, ['label' => 'code élève : '])
            ->add('idrank', IntegerType::class)
            ->add('save', SubmitType::class)
            ->getForm();



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //save ans send action on bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //return administrator panel
            return $this->redirectToRoute('admin');

        }


        return $this->render('updatebyadmin.html.twig', ['update' => $form->createView()]);
    }


}