<?php

namespace App\Tests\entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Skill;

class SkillTest extends TestCase
{
    public function testModel()
    {
        $skill = (new Skill())
            ->setSkill('matiere');

         $this->assertEquals('matiere',$skill->getSkill());
    }
}
