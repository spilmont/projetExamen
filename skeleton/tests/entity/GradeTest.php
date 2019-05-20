<?php

namespace App\Tests\entity;

use App\Entity\Grade;
use App\Entity\Skill;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class GradeTest extends TestCase
{
    public function testModel()
    {
       $grade = (new Grade())
           ->setSkill( new Skill())
           ->setUser(new User())
           ->setGrades(12);


       $this->assertInstanceOf(User::class,$grade->getUser());
       $this->assertInstanceOf(Skill::class,$grade->getSkill());
       $this->assertEquals(12,$grade->getGrades());
}
}
