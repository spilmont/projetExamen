<?php

namespace App\Form;

use App\Entity\Grade;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('users', EntityType::class, [
            'class' => Grade::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u');

            },
            'choice_label' => 'grade',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Grade::class
        ]);
    }
}
