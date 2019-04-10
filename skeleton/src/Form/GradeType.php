<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = new User();

        $builder
            ->add('grades',IntegerType::class,['label'=>$user->getLastname()." ".$user->getFirstname()])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grade::class
        ]);
    }
}
