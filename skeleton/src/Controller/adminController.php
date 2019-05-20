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


use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    //create a new studient with the form
    public function create(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        // create new instance of user object
        $user = new User();

        // create formbuilder for enter information of studient
        if($this->isGranted('ROLE_SUPERADMIN')){
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"nom"]])
                ->add('firstname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"prenom"]])
                ->add('plainPassword', PasswordType::class, ['label' => false,"attr"=>["placeholder"=>"mot de passe"]])
                ->add('usercode', TextType::class, ['label' => false,"attr"=>["placeholder"=>"code élève"]])
                ->add('class',EntityType::class,[
                    "class"=>User::class,
                    'choice_label' => 'class',
                    'query_builder' => function (UserRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->select('s')
                            ->distinct(true)
                            ->groupBy('s.class')
                            ->where('s.idrank = :rank')
                            ->orWhere("s.idrank = 2")
                            ->setParameter('rank',1);
                    },
                    "attr"=>["class"=>"field"]
                ])
                ->add('idrank', ChoiceType::class, ["label"=>false,"choices"=>["Professeur"=>1,"éleve"=>3]])
                ->add('save', SubmitType::class,["attr"=>["class"=>"field"]])
                ->getForm();
        }else{
        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"nom"]])
            ->add('firstname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"prenom"]])
            ->add('plainPassword', PasswordType::class, ['label' => false,"attr"=>["placeholder"=>"mot de passe"]])
            ->add('usercode', TextType::class, ['label' => false,"attr"=>["placeholder"=>"code élève"]])
            ->add('save', SubmitType::class,["attr"=>["class"=>"field"]])
            ->getForm();
    }






        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {

           $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);



            if($this->isGranted('ROLE_SUPERADMIN')) {

                if($form['class']->getData()->getclass() == $this->getUser()->getclass()){
                    $class = $form['class']->getData()->getclass();
                $user->setClass($class);
                    $idprof = $this->getUser()->getId();
                    $user->setIdProf($idprof);
                }
                else{
                    $class = $form['class']->getData()->getclass();
                    $user->setClass($class);
                    $idprof = $this->getDoctrine()->getRepository(User::class)->findOneBy(["idrank"=>1 , "class"=>$form['class']->getData()->getclass()]);
                $user->setIdProf($idprof->getId());
                }
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
        $repouser= $this->getDoctrine()->getRepository(User::class);
        if($this->isGranted('ROLE_ADMIN')){
        $user = $repouser->findby(['idProf'=>$this->getUser()->getid()]);
        }
        elseif($this->isGranted('ROLE_SUPERADMIN')){
            $user = $repouser->findAll();
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
        $repouser = $this->getDoctrine()->getRepository(User::class);
        // entity manager use repository of user class and search user by id
        $user = $repouser->find($id);
        $em = $this->getDoctrine()->getManager();
        // remove  user
        $em->remove($user);
        //save ans send action on bdd
        $em->flush();
        //return administrator panel
        return $this->redirectToRoute('admin', ['id' => $user->getId()]);
    }

    /**
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
                ->add('lastname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"nom"]])
                ->add('firstname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"prenom"]])
                ->add('plainPassword', PasswordType::class, ['label' => false,"attr"=>["placeholder"=>"mot de passe"]])
                ->add('usercode', TextType::class, ['label' => false,"attr"=>["placeholder"=>"code élève"]])
                ->add('class',EntityType::class,[
                    "class"=>User::class,
                    'choice_label' => 'class',
                    'query_builder' => function (UserRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->select('s')
                            ->distinct(true)
                            ->groupBy('s.class')
                            ->where('s.idrank = :rank')
                            ->orWhere("s.idrank = 2")
                            ->setParameter('rank',1);
                    },
                    "attr"=>["class"=>"field"]
                ])
                ->add('idrank', ChoiceType::class, ["label"=>false,"choices"=>["Professeur"=>1,"Directeur"=>2,"éleve"=>3]])
                ->add('save', SubmitType::class,["attr"=>["class"=>"field"]])
                ->getForm();
        }else{
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"nom"]])
                ->add('firstname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"prenom"]])
                ->add('plainPassword', PasswordType::class, ['label' => false,"attr"=>["placeholder"=>"mot de passe"]])
                ->add('usercode', TextType::class, ['label' => false,"attr"=>["placeholder"=>"code élève"]])
                ->add('save', SubmitType::class,["attr"=>["class"=>"field"]])
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            if($this->isGranted('ROLE_SUPERADMIN')) {

                if($form['class']->getData()->getclass() == $this->getUser()->getclass()){
                    $class = $form['class']->getData()->getclass();
                    $user->setClass($class);
                    $idprof = $this->getUser()->getId();
                    $user->setIdProf($idprof);
                }
                else{
                    $class = $form['class']->getData()->getclass();
                    $user->setClass($class);
                    $idprof = $this->getDoctrine()->getRepository(User::class)->findOneBy(["idrank"=>1 , "class"=>$form['class']->getData()->getclass()]);
                    $user->setIdProf($idprof->getId());
                }
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
                ->add('lastname', TextType::class, ['label' => false, 'required' => false,"attr"=>['placeholder'=>"filtrer par nom","class"=>"field"]])
                ->add('class',EntityType::class,[
                    "class"=>User::class,
                    'choice_label' => 'class',
                    'query_builder' => function (UserRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->select('s')
                            ->distinct(true)
                            ->groupBy('s.class')
                           ;
                    },
                    "attr"=>["class"=>"field"]
                ])
                ->add('save', SubmitType::class, ['label' => 'filtrer',"attr"=>["class"=>"field"]])
                ->getForm();


        }else{
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, ['label' => false, 'required' => false,"attr"=>['placeholder'=>"filtrer par nom","class"=>"field"]])
                ->add('save', SubmitType::class, ['label' => 'filtrer',"attr"=>["class"=>"field"]])
                ->getForm();
        }




        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isGranted("ROLE_SUPERADMIN")) {
                $lastname = $form['lastname']->getData();
                $class = $form['class']->getdata()->getclass();


            }
            if ($this->isGranted("ROLE_ADMIN")){
                $lastname = $form['lastname']->getData();
            }
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
        $receiver = $repouser->find($idstudient);

        $repocomments= $this->getDoctrine()->getRepository(Comments::class);
        $comments = $repocomments->findby([],['id'=>'DESC']);

        $form = $this->createFormBuilder($com)
            ->add('comment',TextareaType::class,["label"=>false,"attr"=>['placeholder'=>"entrer un message","class"=>"field"]])
            ->add('envoyer',SubmitType::class,["attr"=>["class"=>"field"]])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $com->setReceiver($receiver);

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

    /**
     * @param $id
     * @Route("/delete/comment/{idstudient}/{id}",name="delete_comment")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
     public function deleteComment(CommentsRepository $commentsRepository,$id, $idstudient)
     {



         $comment = $commentsRepository->find($id);


         $em = $this->getDoctrine()->getManager();
         $em->remove($comment);
         $em->flush();

             return $this->redirectToRoute("message",["idstudient"=>$idstudient]);
         }

    /**
     * @param ObjectManager $manager
     * @param CommentsRepository $commentsRepository
     * @param $id
     * @Route("/update/comment/{idstudient}/{id}",name="update_comment")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
     public function updatecomment(Request $request,ObjectManager $manager,CommentsRepository $commentsRepository, $id,$idstudient){

       $comment =  $commentsRepository->find($id);

       $form = $this->createFormBuilder($comment)
           ->add('comment',TextareaType::class,["label"=>false,"attr"=>["class"=>"field textcom"]])
           ->add('envoyer',SubmitType::class,["attr"=>["class"=>"field"]])
           ->getForm();


        $form->handleRequest($request);
       if ($form->isSubmitted() and $form->isValid() ){

           $manager->flush();
           return $this->redirectToRoute('message',["idstudient"=>$idstudient]);
       }
           return $this->render('updatecomment.html.twig',["comment"=> $form->createView()]);

     }



    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @Route("/admin/createtoclass",name="create_class")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
     public function addclass( Request $request, UserPasswordEncoderInterface $passwordEncoder){

         $user = new User();

         $form = $this->createFormBuilder($user)
             ->add('class',TextType::class,["label"=>false,"attr"=>["placeholder"=>"nouvelle class"]])
             ->add('lastname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"nom du professeur"]])
             ->add('firstname', TextType::class, ['label' => false,"attr"=>["placeholder"=>"prenom du professeur"]])
             ->add('plainPassword', PasswordType::class, ['label' => false,"attr"=>["placeholder"=>"mot de passe"]])
             ->add('usercode', TextType::class, ['label' => false,"attr"=>["placeholder"=>"code utilisateur"]])
             ->add('save', SubmitType::class,["attr"=>["class"=>"field"]])
             ->getForm();

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid() ) {

             $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
             $user->setPassword($password);

             $user->setIdrank("1");

             $em = $this->getDoctrine()->getManager();
             $em->persist($user);
             $em->flush();


             return $this->redirectToRoute('admin');

         }

         return $this->render("admin/createtoclass.html.twig",["class"=> $form->createView()]);
     }

}