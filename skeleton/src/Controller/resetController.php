<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 20/05/2019
 * Time: 21:34
 */

namespace App\Controller;


use App\Entity\User;
use App\Services\Mailer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class resetController extends AbstractController
{

    /**
     * @Route("/reset", name="reset")
     */
   public function reset(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator){


       $form = $this->createFormBuilder()
           ->add('email', EmailType::class,[
            'constraints'=>[
                new Email(),
                new NotBlank()
            ],
       ])
           ->add('envoyer', SubmitType::class,["attr"=>["class"=>"buttonradius"]])
           ->getForm();

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {

           $em = $this->getDoctrine()->getManager();

           $user = $em->getRepository(User::class)->findOneBy(["email" =>$form->getData()['email']]);

           if (!$user) {

               $this->addFlash('error',"l'email n'existe pas ");

               return $this->redirectToRoute("reset");
           }

           $user->setToken($tokenGenerator->generateToken());

           $user->setPasswordRequestedAt(new \DateTime());
           $em->flush();

          $bodyEmail = $mailer->createBodyMail('reset/mail.html.twig',["user"=>$user]);

          $mailer->sendMessage('spilmont204@free.fr', $user->getEmail(),'renouvellement de mot de passe', $bodyEmail);
        ;
           $this->addFlash('success', "Un mail va vous être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous recevrez sera valide 24h.");

           return $this->redirectToRoute("homepage");

       }

       return $this->render('reset/reset.html.twig', [
           'reset' => $form->createView()
             ]);

}


    //the function use in the public function on this controller
    private function ResetTime(\DateTime $passwordResetdAt = null){

       if ($passwordResetdAt == null){
           return false;
       }

       $now = new \DateTime();
       $interval = $now->getTimestamp() - $passwordResetdAt->getTimestamp();

       $time = 60*60*24;
       $reponse = $interval>$time ? false : $reponse = true;
       return $reponse;

    }

    /**
     * @Route("password/{id}/{token}",name="reset_password")
     */
    public function resenting( Request $request,User $user, $token,UserPasswordEncoderInterface $passwordEncoder ){

       if($user->getToken() == null || $token != $user->getToken() || !$this->ResetTime($user->getPasswordRequestedAt())){
           throw new AccessDeniedHttpException();
       }

       $form = $this->createFormBuilder($user)
           ->add('plainPassword',RepeatedType::class,[
               'type'=>PasswordType::class,
               'first_options'=>['label'=>'entrer nouveau mot de passe',"attr"=>["class"=>"fieldgrade "]],
               'second_options'=>['label'=>'retapper le mot de passe',"attr"=>["class"=>"fieldgrade "]],
               'invalid_message'=>"les mots de passe ne s'ont pas identiques"
           ])
           ->add('enregistrer',SubmitType::class,["attr"=>["class"=>'buttonradius']])
           ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('homepage');

        }
        return $this->render('reset/password.html.twig', [
            'password' => $form->createView()
        ]);

    }

    /**
     * @Route("deleteaccount/{id}/{token}",name="delete_account");
     */
    public function deleteaccount( Request $request,User $user, $token ){

        if($user->getToken() == null || $token != $user->getToken() || !$this->ResetTime($user->getPasswordRequestedAt())){
            throw new AccessDeniedHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirect("/");


    }

    /**
     * @Route("/deletemail/{id}", name="delete_mail")
     */
    public function delete(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator,$id){


        $user = $this->getDoctrine()->getRepository(User::class)->find($id);







        $em = $this->getDoctrine()->getManager();

        $user->setToken($tokenGenerator->generateToken());

            $user->setPasswordRequestedAt(new \DateTime());


            $bodyEmail = $mailer->createBodyMail('maildeleteaccount.html.twig',["user"=>$user]);

            $mailer->sendMessage('spilmont204@free.fr', $user->getEmail(),'suppression du compte', $bodyEmail);
        $em->flush();
           $this->addFlash('delete', "Un mail va vous être envoyé afin que vous puissiez suprimer votre compte. Le lien que vous recevrez sera valide 24h.");

        return $this->redirectToRoute("homepage");

        }


}