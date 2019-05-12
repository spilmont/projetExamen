<?php

namespace App\Tests\entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Skill;

class SkillTest extends TestCase
{
    public function testModel()
    {
        $skill = (new Skill())
            ->setSkill('matiere')
            ->setClass('CM2');

         $this->assertEquals('matiere',$skill->getSkill());
         $this->assertEquals('CM2',$skill->getClass());
    }
}
