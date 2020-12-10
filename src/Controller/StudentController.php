<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\EditStudentType;
use App\Form\LoginStudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student")
     */
    public function index(EntityManagerInterface $manager, StudentRepository $repo): Response
    {

        $colonnes = $manager->getClassMetadata(Student::class)->getFieldNames();

        dump($colonnes);

        $users = $this->getUser();

        dump($users);

        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
            'title' => 'Salut salut',
            'colonnes' => $colonnes,
            'users' => $users
        ]);
    }

    /**
     * @Route("student/edit-student", name="edit_student")
     */
    public function newStudent(Request $request): Response
    {
        $student = new Student;

        $studentForm = $this->createForm(EditStudentType::class, $student);

        $studentForm->handleRequest($request);

        // dump($request);

        return $this->render('student/edit_student.html.twig', [
            'studentForm' => $studentForm->createView()
        ]);
    }

    /**
     * @Route("student/login-student", name="login_student")
     */
    public function loginStudent(Request $request): Response
    {
        $student = new Student;

        $loginStudentForm = $this->createForm(LoginStudentType::class, $student);

        $loginStudentForm->handleRequest($request);

        // dump($request);

        return $this->render('student/login_student.html.twig', [
            'loginStudentForm' => $loginStudentForm->createView()
        ]);
    }
}
