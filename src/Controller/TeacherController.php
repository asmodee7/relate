<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Classroom;
use App\Form\StudentType;
use App\Form\ClassroomType;
use Doctrine\ORM\EntityManager;
use App\Repository\TeacherRepository;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="teacher")
     */
    public function index(): Response
    {
        return $this->render('teacher/index.html.twig', [
            'controller_name' => 'TeacherController',
        ]);
    }

    /**
     * @Route("teacher/new_student", name="create_student")
     */
    public function newStudent(Request $request, EntityManagerInterface $manager): Response
    {
            $student = new Student;

        $studentForm = $this->createForm(StudentType:: class, $student);

        $studentForm->handleRequest($request);

        dump($request);

        if($studentForm->isSubmitted() && $studentForm->isValid())
        {
            $manager->persist($student);
            $manager->flush();

            $this->redirectToRoute("create_student");
        }        

        return $this->render("teacher/create_student.html.twig", [
            'studentForm' => $studentForm->createView()
        ]);
    }

    /**
     * @Route("teacher/new_classroom", name="create_classroom")
     */
    public function newClassroom(Request $request, EntityManagerInterface $manager): Response
    {
            $classroom = new Classroom;

        $classroomForm = $this->createForm(ClassroomType:: class, $classroom);

        $classroomForm->handleRequest($request);

        // dump($request);
        // dump($this->getUser()->getUsername());

        if($classroomForm->isSubmitted() && $classroomForm->isValid())
        {
            $classroom->addTeacher($this->getUser());
            // dump($classroom->getTeachers());
            $manager->persist($classroom);
            $manager->flush();

            $this->redirectToRoute("create_classroom");
        }        

        return $this->render("teacher/create_classroom.html.twig", [
            'classroomForm' => $classroomForm->createView()
        ]);

    }

    /**
     * @Route("teacher/assoc_classroom", name="assoc_classroom")
     */
    public function show(ClassroomRepository $repo, EntityManagerInterface $manager, TeacherRepository $repoTeacher): Response
    {
        $titres = $manager->getClassMetadata(Classroom::class)->getFieldNames();
        $classrooms = $repo->findAll();

        $user = $this->getUser()->getId();

        $mesCLasses = $repoTeacher->find($user);
        // dump($classrooms);

        // dump($manager);

        $autresClasses = $manager->createQuery("SELECT * FROM Teacher WHERE country = 'français' AND grade = '6'");

        dump($autresClasses);

        // $autresClasses = $repo->findAllExcept($user);

        //$myClassrooms = $repo->findBy(array ('teachers' => $teacher));

        // Doit afficher les classes du prof
        // Doit afficher les classes des autres écoles
        return $this->render("teacher/assoc_classroom.html.twig", [
            'titres' => $titres,
            'classrooms' => $classrooms,
            'mesCLasses' => $mesCLasses,
            'autresClasses' => $autresClasses
        ]);
    }
}
