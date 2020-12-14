<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('lastname')
            ->add('firstname')
            ->add('age')
            ->add('description')
            ->add('photo')
            ->add('sport')
            ->add('music')
            ->add('OtherHobbies')
            // ->add('classrooms', EntityType::class, [
            //     'label' => 'Classroom',
            //     'class' => Classroom::class,
            //     // "query_builder" => function (ClassroomRepository $repo) use ($user) {
            //     //     return $repo->getClassroomsUser($user);
            //     // },
            //     'choice_label' => 'classroom_name',
            //     'expanded' => true,
            //     'multiple' => true

            // ])
            // ->add('studentDuos')
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,

        ]);
    }
}
