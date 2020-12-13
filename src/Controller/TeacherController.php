<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Classroom;
use App\Entity\ClassroomDuo;
use App\Form\ClassroomDuoType;
use App\Form\StudentType;
use App\Form\ClassroomType;
use App\Form\EditTeacherType;
use App\Repository\ClassroomDuoRepository;
use Doctrine\ORM\EntityManager;
use App\Repository\TeacherRepository;
use App\Repository\ClassroomRepository;
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
    public function newStudent(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $student = new Student;

        $studentForm = $this->createForm(StudentType::class, $student);

        $studentForm->handleRequest($request);

        dump($request);

        if ($studentForm->isSubmitted() && $studentForm->isValid()) {
            $hash = $encoder->encodePassword($student, $student->getPassword());
            $student->setPassword($hash);
           
            $student->setRoles(["ROLE_STUDENT"]);
            $manager->persist($student);
            $manager->flush();

            $this->redirectToRoute("create-student");
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

        $classroomForm = $this->createForm(ClassroomType::class, $classroom);

        $classroomForm->handleRequest($request);

        // dump($request);

        // dump($this->getUser()->getUsername());

        if ($classroomForm->isSubmitted() && $classroomForm->isValid()) {
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

    public function classduostudents(ClassroomDuo $newclassroomDuo, ClassroomDuoRepository $duorepo, ClassroomRepository $classroomrepo): Response
    {

        // on récupère l'id du classroomDuo

        // on montre le tableau qui correspond à l'id du classroomDuo avec classroom_1 et classroom_2

        // on affiche la classroom_id qui correspond au chiffre dans classroom_1

        // on verra après pour classroom_2 et les conditions

        // $classroomduo = $duorepo->findByid();

        $classroomduo = $duorepo->findAll();


        foreach ($classroomduo as $classroomduodata) {
            $id = $classroomduodata->getId();
        }
        dump($id);

        foreach ($classroomduo as $classroomduodata) {
            $classroom1 = $classroomduodata->getClassroom1();
        }
        dump($classroom1); // c'est "4" pour le duoclassroom 5

        foreach ($classroomduo as $classroomduodata) {
            $classroom2 = $classroomduodata->getClassroom2();
        }
        dump($classroom2);

        $classroom = $classroomrepo->findById($classroom1); // il est allé chercher la classroom 4

        dump($classroom); // on a toutes les infos de la classroom_1 qui a l'id classroom 4


        // $classroom->unwrap()->toArray();

        // dump($classroom);


        return $this->render("teacher/assoc_student.html.twig", [
            'classroom' => $classroom,
            'newclassroomDuo' => $newclassroomDuo
            // 'myclassrooms' => $myclassrooms
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
     * @Route("teacher/{id}/my_classroomduos", name="teacher_showclassroom")
     */
    public function showTeacherClassroomDuos(Request $request, ClassroomDuoRepository $duorepo, ClassroomRepository $classrepo)
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


        return $this->render("teacher/teacher_showclassrooms.html.twig", [
            'classroomDuo' => $classroomDuo,
            'myclassroom' => $myclassroom
        ]);
    }

    /**
     * @Route("/teacher/profile/{id}", name="my_teacher_profile")
     */
    public function showProfile(TeacherRepository $repo, $id)
    {
        /* $repo =$this->getDoctrine()-> getRepository(Teacher::class); */

        $teacher =$repo->find($id);

        return $this->render('teacher/profile.html.twig', 
        [
            'teacher' => $teacher
        ]);
    }

    /**
     * @Route("/teacher/edit/{id}", name="edit_my_teacher_profile")
     */
    public function edit(Teacher $teacher, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(EditTeacherType::class, $teacher);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($teacher);
            $manager->flush();

            return $this->redirectToRoute('my_teacher_profile', ['id' => $teacher->getId()]);
        }

        return $this->render('teacher/editprofile.html.twig',
        [
            'formEditTeacher' => $form->createView()
        ]);
    }
}
