<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Classroom;
use App\Form\StudentType;
use App\Entity\StudentDuo;
use App\Form\ClassroomType;
use App\Entity\ClassroomDuo;
use App\Form\EditTeacherType;
use App\Form\ClassroomDuoType;
use Doctrine\ORM\EntityManager;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use App\Repository\ClassroomRepository;
use App\Repository\StudentDuoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClassroomDuoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function newStudent(Request $request,SluggerInterface $slugger, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, TeacherRepository $teacherrepo, ClassroomRepository $classroomrepo): Response
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

        if ($studentForm->isSubmitted() && $studentForm->isValid()) 
        {
            $photoFile = $studentForm->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newPhotoFile = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('photo_directory'),
                        $newPhotoFile
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $student->setPhoto($newPhotoFile);
            }

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

    public function classduostudents(ClassroomDuo $newclassroomDuo, ClassroomDuoRepository $duorepo, ClassroomRepository $classroomrepo, Request $classRoomDuoRequest, EntityManagerInterface $manager, Request $request, StudentRepository $studentrepo, StudentDuoRepository $studentduorepo): Response
    {

        // on récupère l'id du classroomDuo
        // on montre le tableau qui correspond à l'id du classroomDuo avec classroom_1 et classroom_2
        // on affiche la classroom_id qui correspond au chiffre dans classroom_1
        // on verra après pour classroom_2 et les conditions


        $students = $studentduorepo->findAll();

        dump($students);


        // on récupère l'id du classroomDuo
        $classroomduo = $classRoomDuoRequest->attributes->get('id');
        // dump($classroomduo);

        // on affiche la classroom_id qui correspond au chiffre dans classroom_1
        $classroom1 = $newclassroomDuo->getClassroom1($classroomduo);

        // dump($classroom1); // id classe 1 en fonction de l'id

        // on affiche la classroom_id qui correspond au chiffre dans classroom_2
        $classroom2 = $newclassroomDuo->getClassroom2($classroomduo);

        // dump($classroom2); // id classe 2 en fonction de l'id

        $classroomOne = $classroomrepo->findById($classroom1); // il est allé chercher la classroom n dans classroom1


        // dump($classroomOne); // on a toutes les infos de la classroom_1 qui a l'id classroom n

        $classroomTwo = $classroomrepo->findById($classroom2);
        // dump($classroomTwo);

        $id = $request->get('id');
        // dump($id);

        $studentsFromClassroom1 = $studentrepo->findStudentsByClassroom($classroom1);
        // dump($studentsFromClassroom1);

        $studentsFromClassroom2 = $studentrepo->findStudentsByClassroom($classroom2);
        // dump($studentsFromClassroom2);

        $studentsDuos = $studentduorepo->findAll();
        // dump($studentsDuos);

        // $student1 = $studentsDuos->getStudent1();
        // dump($student1);

        // ----------------------------------------------------------------------------------------------

        // ClassroomDuo dans l'URL : $classroomduo
        // Class from $classroomduo : $classFromDuo = $duorepo->find($classroomduo)

        // dump($duorepo->find($classroomduo)->getClassroom1());
        // dump($duorepo->find($classroomduo)->getClassroom2());

        // Classroom 1 :$classroom1
        // Classroom 2 : $classroom2
        // Students classroom 1 : $studentsFromClassroom1
        // Students classroom 2 : $studentsFromClassroom2
        // StudentDuos : $studentsDuos

        // dump($studentsFromClassroom1->getId());

        $students1FromDuo = [];
        $students2FromDuo = [];

        foreach ($studentsDuos as $oneStudentDuo) {
            // dump($oneStudentDuo->getStudent1());
            // dump($oneStudentDuo->getStudent2());

            // dump($studentsFromClassroom1);

            foreach ($studentsFromClassroom1 as $oneStudentFromClassroom1) {
                // dump($oneStudentFromClassroom1);
                // dump($oneStudentDuo->getStudent1());

                if ($oneStudentDuo->getStudent1() == $oneStudentFromClassroom1->getId() || $oneStudentDuo->getStudent2() == $oneStudentFromClassroom1->getId()) {

                    $students1FromDuo[] = $oneStudentFromClassroom1;

                    foreach($oneStudentFromClassroom1->getClassrooms() as $oneClassroom)
                    {
                        dump($oneClassroom->getId());
                    }

                }
            }

            foreach ($studentsFromClassroom2 as $oneStudentFromClassroom2) {
                // dump($oneStudentFromClassroom1);
                // dump($oneStudentDuo->getStudent1());

                if ($oneStudentDuo->getStudent2() == $oneStudentFromClassroom2->getId() || $oneStudentDuo->getStudent1() == $oneStudentFromClassroom2->getId()) {
                    $students2FromDuo[] = $oneStudentFromClassroom2;
                }
            }

            // dump($test1);
            // dump($test2);

        }

        dump($students1FromDuo);
        dump($students2FromDuo);


        // dump($classroomTwo);


        /********************************************************************* */

        // SECURISATION URL
        // dump($this->getUser());

        // aller chercher les classes du prof
        $tabId = [];
        for ($i = 0; $i < count($this->getUser()->getClassrooms()); $i++) {
            $tabId[] = $this->getUser()->getClassrooms()[$i]->getId();
        }
        dump($tabId);
        // $tabId donne un array des id des classrooms du prof connecté

        // dump($newclassroomDuo);
        // là on regarde quelles classes sont dans le classroomduo

        // $classroom1 et $classroom2 permettent de récupérer l'id des classrooms du duo

        // Si les classes du classroomduo de l'url ne correspondent à aucune classe du prof, ça renvoie à l'affichage des classroms du prof connecté
        if (!in_array($classroom1, $tabId) && !in_array($classroom2, $tabId)) {
            return $this->redirectToRoute('teacher_classrooms');
        }

        if ($classRoomDuoRequest->request->count() > 1) {
            $studentDuo = new StudentDuo();

            // si ce qu'il y a dans le select correspond à quelque chose dans la table student, alors retourne moi son ID
            // envoie l'ID dans la table de DUO students

            $studentDuo->setStudent1($classRoomDuoRequest->request->get('student_1'))
                ->setStudent2($classRoomDuoRequest->request->get('student_2'));

            $manager->persist($studentDuo);
            $manager->flush();

            return $this->redirectToRoute('assoc_student', ['id' => $classroomduo]);
        }

        return $this->render("teacher/assoc_student.html.twig", [
            'classroomOne' => $classroomOne,
            'classroomTwo' => $classroomTwo,
            'newclassroomDuo' => $newclassroomDuo,
            'studentsDuos' => $studentsDuos,
            'studentsFromClassroom1' => $studentsFromClassroom1,
            'studentsFromClassroom2' => $studentsFromClassroom2,
            'students1FromDuo' => $students1FromDuo,
            'students2FromDuo' => $students2FromDuo

        ]);
    }


    /**
     * @Route("teacher/{id}/my_classroomduos", name="teacher_showclassroom")
     */
    public function showTeacherClassroomDuos(Request $request, ClassroomDuoRepository $duorepo, ClassroomRepository $classrepo, StudentRepository $studentrepo)
    {

        // dump($this->getUser()->getClassrooms()[0]);

        $id = $request->get('id');
        dump($id); // on récupère l'id de la classroom dans l'url, qui est le même que l'id de classroom_1

        // SECURISATION URL
        $tabId = [];
        for ($i = 0; $i < count($this->getUser()->getClassrooms()); $i++) {
            $tabId[] = $this->getUser()->getClassrooms()[$i]->getId();
        }
        // on va chercher l'array de tous les id des classes du prof

        $position = array_search($id, $tabId);
        dump($tabId);
        // on va chercher si l'id entré dans l'URL correspond aux id des classes du prof

        if ($position === false) {
            return $this->redirectToRoute('teacher_classrooms');
        }
        // si c'est faux, c'est que l'id entré ne correspond pas aux id des classes du prof, ça redirige sur teacher_classrooms




        // MONTRER LES CLASSROOMS PARTENAIRES selon l'id de classroom_1 & 2

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

        $userid = $this->getUser()->getId();
        // dump($userid);

        $teacher = $repo->find($id);

        $teacherid = $teacher->getId(); // on récupère l'id de teacher dans l'URL
        // dump($teacherid);

        // SECURISATION URL
        if ($userid != $teacherid) {
            return $this->redirectToRoute('homepage');
        }

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
    public function edit(Teacher $teacher, Request $request, SluggerInterface $slugger, EntityManagerInterface $manager)
    {

        

        $userid = $this->getUser()->getId();
        dump($userid); // id du teacher connecté

        $urlid = $request->attributes->get('id');

        // SECURISATION URL
        if ($userid != $urlid) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(EditTeacherType::class, $teacher);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) 
            {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newPhotoFile = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('photo_directory'),
                        $newPhotoFile
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $teacher->setPhoto($newPhotoFile);
            }
            
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
