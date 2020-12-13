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
     * @Route("/student/", name="studenthomepage")
     */
    public function index()
    {
        return $this->render('student/index.html.twig');
    }

    /**
     * @Route("/student/profile/{id}", name="my_student_profile")
     */
    public function showProfile($id, Request $request, EntityManagerInterface $manager)
    {
        $repo =$this->getDoctrine()-> getRepository(Student::class);

        $student =$repo->find($id);

        return $this->render('student/profile.html.twig', 
        [
            'student' => $student
        ]);
    }

    /**
     * @Route("/student/edit/{id}", name="edit_my_student_profile")
     */
    public function edit(Student $student, Request $request, EntityManagerInterface $manager)
    {


        $form = $this->createForm(EditStudentType::class, $student);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($student);
            $manager->flush();

            return $this->redirectToRoute('my_student_profile', ['id' => $student->getId()]);
        }

        return $this->render('student/edit.html.twig',
        [
            'formEditStudent' => $form->createView()
        ]);
    }
}