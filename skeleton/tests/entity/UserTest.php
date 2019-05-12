<?php

namespace App\Tests\entity;

use PHPUnit\Framework\TestCase;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testModel(){
        $user = (new User())
            ->setFirstname("prenom")
            ->setLastname("nom")
            ->setIdrank(3)
            ->setIdProf(1)
            ->setClass("CM2")
            ->setUsercode('00000')
            ->setPassword('dd')
        ;
        $this->assertEquals("prenom",$user->getFirstname());
        $this->assertEquals("nom",$user->getLastname());
        $this->assertEquals(3,$user->getIdrank());
        $this->assertEquals(1,$user->getIdProf());
        $this->assertEquals("CM2",$user->getClass());
        $this->assertEquals("00000",$user->getUsercode());
    }
}
