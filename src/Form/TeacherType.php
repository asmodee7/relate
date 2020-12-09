<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\Teacher;
use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('lastname')
            ->add('firstname')
            ->add('language')
            ->add('email')
            ->add('phone_number')
            ->add('photo')
            ->add('description')
            ->add('id_school', EntityType::class, [
                'label' => 'School',
                'class' => School::class,
                'choice_label' => 'school_name'
                ])
            ->add('classrooms', EntityType::class,[
                'label' => 'Classroom',
                'class' => Classroom::class,
                'choice_label' => 'classroom_name',
                'expanded' => true,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
