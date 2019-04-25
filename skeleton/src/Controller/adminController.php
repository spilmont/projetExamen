<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 17/02/2019
 * Time: 20:22
 */

// controller for create a new studient use object user
namespace App\Controller;


use App\Entity\Comments;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// controller for CRUD For  administrator panel


class adminController extends AbstractController
{



    /**
     * @Route("/admin/createuserbyadmin", name="user_by_admin")
     */
    public function create(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        //create a new studient with the form


        // create new instance of user object
        $user = new User();

        // create formbuilder for enter information of studient
        if($this->isGranted('ROLE_SUPERADMIN')){
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => 'nom : '])
                ->add('firstname', TextType::class, ['label' => 'prénom : '])
                ->add('plainPassword', PasswordType::class, ['label' => 'mot de passe : '])
                ->add('usercode', TextType::class, ['label' => 'code élève : '])
                ->add('class',ChoiceType::class,["label"=>false,"choices"=>["CP"=>"CP","CE1"=>"CE1","CE2"=>"CE2","CM1"=>"CM1","CM2"=>"CM2"]])
                ->add('idrank', ChoiceType::class, ["label"=>false,"choices"=>["Professeur"=>1,"Directeur"=>2,"éleve"=>3]])
                ->add('save', SubmitType::class)
                ->getForm();
        }else{
        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class, ['label' => 'nom : '])
            ->add('firstname', TextType::class, ['label' => 'prénom : '])
            ->add('plainPassword', PasswordType::class, ['label' => 'mot de passe : '])
            ->add('usercode', TextType::class, ['label' => 'code élève : '])
            ->add('save', SubmitType::class)
            ->getForm();
    };






        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {

           $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);



            if($this->isGranted('ROLE_SUPERADMIN')) {

                if($form['class']->getData() == $this->getUser()->getclass())
                    $idprof = $this->getUser();
                else
                    $idprof = $this->getDoctrine()->getRepository(User::class)->findOneBy(["idrank"=>1 , "class"=>$form['class']->getData()]);

                $user->setIdProf($idprof->getId());
            }

            if($this->isGranted('ROLE_ADMIN')) {
                $user->setClass($this->getUser()->getclass());
                $user->setIdProf($this->getUser()->getid());
                $user->setIdrank("3");
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('admin');

        }


        return $this->render('admin/createuseradmin.html.twig', [
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
        if($this->isGranted('ROLE_ADMIN')){
        $user = $repository->findby(['idProf'=>$this->getUser()->getid()]);
        }
        elseif($this->isGranted('ROLE_SUPERADMIN')){
            $user = $repository->findAll();
        }
        return $this->render("admin/admin.html.twig", ["user" => $user]);


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
    public function update(Request $request,UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        // for update user by admin


        // use entity manager
        $em = $this->getDoctrine()->getManager();

        // entity manager use repository of user class and search user by id
        $user = $em->getRepository(User::class)->find($id);


        if($this->isGranted('ROLE_SUPERADMIN')){
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => 'nom : '])
                ->add('firstname', TextType::class, ['label' => 'prénom : '])
                ->add('plainPassword', PasswordType::class, ['label' => 'mot de passe : '])
                ->add('usercode', TextType::class, ['label' => 'code élève : '])
                ->add('class',ChoiceType::class,["choices"=>["CP"=>"CP","CE1"=>"CE1","CE2"=>"CE2","CM1"=>"CM1","CM2"=>"CM2"]])
                ->add('idrank', IntegerType::class, ['attr' => ['min' => 1, 'max' => 3]])
                ->add('save', SubmitType::class)
                ->getForm();
        }else{
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => 'nom : '])
                ->add('firstname', TextType::class, ['label' => 'prénom : '])
                ->add('PlainPassword', PasswordType::class, ['label' => 'mot de passe : '])
                ->add('usercode', TextType::class, ['label' => 'code élève : '])
                ->add('save', SubmitType::class)
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            if($this->isGranted('ROLE_SUPERADMIN')) {

                if($form['class']->getData() == $this->getUser()->getclass())
                    $idprof = $this->getUser();
                else
                    $idprof = $this->getDoctrine()->getRepository(User::class)->findOneBy(["idrank"=>1 , "class"=>$form['class']->getData()]);

                $user->setIdProf($idprof->getId());
            }

            if($this->isGranted('ROLE_ADMIN')) {
                $user->setClass($this->getUser()->getclass());
                $user->setIdProf($this->getUser()->getid());
                $user->setIdrank("3");
            }

            //save ans send action on bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //return administrator panel
            return $this->redirectToRoute('admin');

        }


        return $this->render('admin/updatebyadmin.html.twig', ['update' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin",name="admin")
     */
    public function filter(Request $request)
    {

        $lastname = NULL;
        $class = null;

        $em = $this->getDoctrine()->getManager();

        if($this->isGranted('ROLE_ADMIN')){
            $user = $em->getRepository(User::class)->findBy(['idProf'=>$this->getUser()->getid()]);
        }
        elseif($this->isGranted('ROLE_SUPERADMIN')) {
            $user = $em->getRepository(User::class)->findAll();
        }

        if($this->isGranted('ROLE_SUPERADMIN')) {
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => 'nom', 'required' => false])
                ->add('class', ChoiceType::class, ['label' => 'classe', 'required' => false,
                    'choices' => ["tous" => "", "CP" => "CP", 'CE1' => 'CE1', 'CE2' => 'CE2', 'CM1' => 'CM1', 'CM2' => 'CM2']])
                ->add('save', SubmitType::class, ['label' => 'filtrer'])
                ->getForm();
        }else{
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => 'nom', 'required' => false])
                ->add('save', SubmitType::class, ['label' => 'filtrer'])
                ->getForm();
        }




        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lastname = $form['lastname']->getData();
            $class = $form['class']->getData();
        }

       return  $this->render('admin/admin.html.twig', ['user' => $user,
            'filter' => $form->createView(),
            'lastname' => $lastname,
            'class'=> $class
        ]);

    }

    /**
     * @Route("/admin/message/{idstudient}",name="message")
     */
    public function  comment(Request $request,$idstudient ){
        $com = new Comments();


        $repouser = $this->getDoctrine()->getRepository(User::class);
        $sender = $repouser->find($idstudient);


        $repocomments= $this->getDoctrine()->getRepository(Comments::class);
        $comments = $repocomments->findby([],['id'=>'DESC']);



        $form = $this->createFormBuilder($com)
            ->add('comment',TextareaType::class)
            ->add('save',SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $com->setReceiver($sender);

            $com->setSender($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($com);
            $em->flush();

            return  $this->redirectToRoute('message',['idstudient'=>$idstudient]);


        }
        return $this->render("admin/comments.html.twig",['sender'=>$idstudient,'comments'=>$comments,'formCom'=>$form->createView()]);
    }

    /**
     * @Route("/admin/ajaxlink/{id}", name="ajax_link")
     */
    public  function ajaxlink($id){

        $repouser= $this->getDoctrine()->getRepository(User::class);
        $user = $repouser->find($id);

        if($user->getidrank() ==3 and $this->getUser()->getclass() == $user->getclass()){

            $idgrade = $user->getid();
        }
        else{
            $idgrade = "hidden";
        }


        return $this->json(["nom"=>$user->getlastname(),'prenom'=>$user->getfirstname(),
            'id'=>$user->getid(),"idgrade"=>$idgrade

                    ]);











    }
}