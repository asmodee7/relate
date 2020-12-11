<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\EditStudentType;
use App\Form\LoginStudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentController extends AbstractController
{
    /**
     * @Route("/student/", name="student_homepage")
     */
    public function index()
    {
        return $this->render('student/index.html.twig');
    }

    /**
     * @Route("/student/profile", name="me_student_profile")
     */
    public function showProfile(Student $student)
    {
        return $this->render('student/profile.html.twig', 
        [
            'student' => $student
        ]);
    }

    /**
     * @Route("/student/edit", name="edit-student")
     */
    public function edit(Request $request, EntityManagerInterface $manager)
    {
        $student = new Student();


        $form = $this->createFormBuilder($student)
                    ->add('firstname')
                    ->add('lastname')
                    ->add('age')
                    ->add('photo')
                    ->add('description')
                    ->add('sport')
                    ->add('music')
                    ->add('OtherHobbies')
                    ->add('studentDuos')
                    ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($student);
            $manager->flush();

            return $this->redirectToRoute('me_student_profile', ['id' => $student->getId()]);
        }

        return $this->render('student/edit.html.twig',
        [
            'formEditStudent' => $form->createView()
        ]);
    }
}