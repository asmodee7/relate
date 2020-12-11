<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\ClassroomDuo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassroomDuoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('classroom_1', EntityType::class,[
                'label' => 'Mes classes',
                'class' => Classroom::class,
                'choice_label' => 'classroom_name',
                'expanded' => true,
                'multiple' => true
            ])
            ->add('classroom_2', EntityType::class,[
                'label' => 'Classe disponibles',
                'class' => Classroom::class,
                'choice_label' => 'classroom_name',
                'expanded' => true,
                'multiple' => true
            ])
            // ->add('classrooms')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClassroomDuo::class,
        ]);
    }
}
