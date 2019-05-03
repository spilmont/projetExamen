<?php

namespace App\Tests\entity;


use App\Entity\Comments;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Date;

class CommentTest extends TestCase
{
    public function testModel()
    {
       $comment = (new Comments())
        ->setSender(new User())
        ->setReceiver(new User())
        ->setComment('salut')
        ->setDate(\date('d/m/Y',time())) ;

       $this->assertInstanceOf(User::class,$comment->getSender());
       $this->assertInstanceOf(User::class,$comment->getReceiver());
       $this->assertEquals('salut',$comment->getComment());
       $this->assertEquals(\date('d/m/Y',time()),$comment->getDate());
    }
}
