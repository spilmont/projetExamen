<?php
/**
 * Created by PhpStorm.
 * User: Onatouvus
 * Date: 14/02/2019
 * Time: 19:50
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//main controller for show the different page of website

class mainController extends AbstractController
{
    /**
     * @return mixed
     * @Route("/",name="homepage")
     */
    public function homepage()
    {
        //   return the homepage view
        return $this->render("home.html.twig");
    }



    /**
     * @Route("/user",name="user")
     * */
    public function user()
    {
        // return the user page
        return $this->render('user.html.twig');
    }









}