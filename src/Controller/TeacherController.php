<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Classroom;
use App\Entity\ClassroomDuo;
use App\Entity\StudentDuo;
use App\Form\ClassroomDuoType;
use App\Form\StudentType;
use App\Form\ClassroomType;
use App\Form\EditTeacherType;
use App\Repository\ClassroomDuoRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\TeacherRepository;
use App\Repository\ClassroomRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("teacher/new-student", name="create-student")
     */
    public function newStudent(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, TeacherRepository $teacherrepo, ClassroomRepository $classroomrepo): Response
    {
        $user = $this->getUser()->getId(); // id du teacher connecté
        dump($this->getUser()->getClassrooms());

        $student = new Student;
        dump($student);

        $studentForm = $this->createForm(StudentType::class, $student);

        $studentForm->handleRequest($request);

        dump($request);

        $myclassrooms = $classroomrepo->getClassroomsUser($user); // on va chercher les infos de classroomrepo en fonction de l'id du prof
        // dump($myclassrooms);

        if ($studentForm->isSubmitted() && $studentForm->isValid()) {

            dump($request->request->get('classrooms'));

            $id = $request->request->get('classrooms');

            $classroomTest = $classroomrepo->find($id);
            dump($classroomTest);

            $student->addClassroom($classroomTest);

            $hash = $encoder->encodePassword($student, $student->getPassword());
            $student->setPassword($hash);

            $student->setRoles(["ROLE_STUDENT"]);

            $manager->persist($student);
            $manager->flush();

            $this->redirectToRoute("create-student");
        }

        return $this->render("teacher/create_student.html.twig", [
            'studentForm' => $studentForm->createView(),
            'myclassrooms' => $myclassrooms
        ]);
    }

    /**
     * @Route("teacher/my_classrooms", name="teacher_classrooms")
     */
    public function showTeacherClassrooms(TeacherRepository $repo)
    {
        $user = $this->getUser()->getId();

        $myClassrooms = $repo->find($user);

        dump($myClassrooms);

        return $this->render("teacher/teacher_classrooms.html.twig", [
            'myClassrooms' => $myClassrooms
        ]);
    }

    /**
     * @Route("teacher/new_classroom", name="create_classroom")
     */
    public function newClassroom(Request $request, EntityManagerInterface $manager): Response
    {
        $classroom = new Classroom;

        $classroomForm = $this->createForm(ClassroomType::class, $classroom);

        $classroomForm->handleRequest($request);

        // dump($request);

        // dump($this->getUser()->getUsername());

        if ($classroomForm->isSubmitted() && $classroomForm->isValid()) {
            $classroom->addTeacher($this->getUser());
            // dump($classroom->getTeachers());
            $manager->persist($classroom);
            $manager->flush();

            return $this->redirectToRoute("teacher_classrooms");
        }

        return $this->render("teacher/create_classroom.html.twig", [
            'classroomForm' => $classroomForm->createView()
        ]);
    }

    /**
     * @Route("teacher/assoc_classroom", name="assoc_classroom")
     */
    public function show(ClassroomRepository $repo, EntityManagerInterface $manager, TeacherRepository $repoTeacher, Request $classRoomDuoRequest): Response
    {
        $titres = $manager->getClassMetadata(Classroom::class)->getFieldNames();

        $user = $this->getUser()->getId(); // id du teacher connecté

        $mesCLasses = $repoTeacher->find($user); // infos du teacher en fonction de son id


        $language = $this->getUser()->getLanguage(); // language du teacher connecté

        $classrooms = $repo->getClassrooms($user, $language); // on va chercher les infos de classroomrepo en fonction de l'id et de la langue du prof

        // Envoi du formulaire de jumelage

        dump($classRoomDuoRequest);

        if ($classRoomDuoRequest->request->count() > 1) {
            $classRoomDuo = new ClassroomDuo();
            $classRoomDuo->setClassroom1($classRoomDuoRequest->request->get('classroom_1'))
                ->setClassroom2($classRoomDuoRequest->request->get('classroom_2'));

            $manager->persist($classRoomDuo);
            $manager->flush();

            return $this->redirectToRoute('assoc_student', ['id' => $classRoomDuo->getId()]);
        }


        return $this->render("teacher/assoc_classroom.html.twig", [
            'titres' => $titres,
            'classrooms' => $classrooms,
            'mesCLasses' => $mesCLasses,
        ]);
    }

    /**
     * @Route("teacher/{id}/assoc_student", name="assoc_student")
     */

    public function classduostudents(ClassroomDuo $newclassroomDuo, ClassroomDuoRepository $duorepo, ClassroomRepository $classroomrepo, Request $classRoomDuoRequest, EntityManagerInterface $manager): Response
    {

        // on récupère l'id du classroomDuo

        // on montre le tableau qui correspond à l'id du classroomDuo avec classroom_1 et classroom_2

        // on affiche la classroom_id qui correspond au chiffre dans classroom_1

        // on verra après pour classroom_2 et les conditions

        $classroomduo = $classRoomDuoRequest->attributes->get('id');
        dump($classroomduo);

        $classroom1 = $newclassroomDuo->getClassroom1($classroomduo);

        dump($classroom1); // id classe 1 en fonction de l'id

        $classroom2 = $newclassroomDuo->getClassroom2($classroomduo);

        dump($classroom2); // id classe 2 en fonction de l'id

        $classroomOne = $classroomrepo->findById($classroom1); // il est allé chercher la classroom n dans classroom1

        dump($classroomOne); // on a toutes les infos de la classroom_1 qui a l'id classroom n

        $classroomTwo = $classroomrepo->findById($classroom2);

        dump($classroomTwo);

        // dump($studentDuoRequest);

        if ($classRoomDuoRequest->request->count() > 1) {
            $studentDuo = new StudentDuo();

            // si ce qu'il y a dans le select correspond à quelque chose dans la table student, alors retourne moi son ID
            // envoie l'ID dans la table de DUO

            $studentDuo->setStudent1($classRoomDuoRequest->request->get('student_1'))
                ->setStudent2($classRoomDuoRequest->request->get('student_2'));

            $manager->persist($studentDuo);
            $manager->flush();

            // return $this->redirectToRoute('assoc_student', ['id' => $studentDuo->getId()]);
        }

        // RAJOUTER IF CLASSROOM ID CORRESPOND AU PROF CONNECTE

        return $this->render("teacher/assoc_student.html.twig", [
            'classroomOne' => $classroomOne,
            'classroomTwo' => $classroomTwo,
            'newclassroomDuo' => $newclassroomDuo
        ]);
    }


    /**
     * @Route("teacher/{id}/my_classroomduos", name="teacher_showclassroom")
     */
    public function showTeacherClassroomDuos(Request $request, ClassroomDuoRepository $duorepo, ClassroomRepository $classrepo, StudentRepository $studentrepo)
    {

        $id = $request->get('id');

        dump($id); // on récupère l'id de la classroom dans l'url, qui est le même que l'id de classroom_1

        // on montre les partenaires selon l'id de classroom_1

        // TROUVER TOUTES LES CLASSROOMS_1 ET CLASSROOM_2 AVEC ID URL
        // getClassroomDuoTeacher() dans ClassroomDuoRepository permet d'aller chercher les classroomDuos où les classroom_1 et classroom_2 correspondent à l'id de la classroom sélectionnée

        // look for *all* Product objects
        // $products = $duorepo->findAll();

        $classroomDuo = $duorepo->getClassroomDuoTeacher($id);

        dump($classroomDuo);

        $myclassroom = $classrepo->findById($id);

        dump($myclassroom);

        $mystudents = $studentrepo->findStudentsByClassroom($id);

        dump($mystudents);

        // on veut les classrooms où l'id de classroom dans étudiant = ID de la classroom

        return $this->render("teacher/teacher_classroom_info.html.twig", [
            'classroomDuo' => $classroomDuo,
            'myclassroom' => $myclassroom,
            'mystudents' => $mystudents
        ]);
    }

    /**
     * @Route("/teacher/profile/{id}", name="my_teacher_profile")
     */
    public function showProfile(TeacherRepository $repo, $id)
    {
        /* $repo =$this->getDoctrine()-> getRepository(Teacher::class); */

        $teacher = $repo->find($id);

        return $this->render(
            'teacher/profile.html.twig',
            [
                'teacher' => $teacher
            ]
        );
    }

    /**
     * @Route("/teacher/edit/{id}", name="edit_my_teacher_profile")
     */
    public function edit(Teacher $teacher, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(EditTeacherType::class, $teacher);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($teacher);
            $manager->flush();

            return $this->redirectToRoute('my_teacher_profile', ['id' => $teacher->getId()]);
        }

        return $this->render(
            'teacher/editprofile.html.twig',
            [
                'formEditTeacher' => $form->createView()
            ]
        );
    }
}
