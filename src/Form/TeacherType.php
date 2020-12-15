<?php

namespace App\Form;

use App\Entity\School;
use App\Entity\Teacher;
use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('lastname')
            ->add('firstname')
            ->add('language')
            ->add('email')
            ->add('phone_number')
            ->add('photo', FileType::class, [
                'label' => 'Image (Jpeg,png,gif file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid ImageFiles',
                    ])
                ],
            ])
            ->add('description')
            // ->add('id_school', EntityType::class, [
            //     'label' => 'School',
            //     'class' => School::class,
            //     'choice_label' => 'school_name'
            //     ])
            // ->add('classrooms', EntityType::class,[
            //     'label' => 'Classroom',
            //     'class' => Classroom::class,
            //     'choice_label' => 'classroom_name',
            //     'expanded' => true,
            //     'multiple' => true
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
