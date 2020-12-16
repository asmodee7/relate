<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\EditStudentType;
use App\Form\LoginStudentType;
use App\Repository\StudentDuoRepository;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class StudentController extends AbstractController
{
    /**
     * @Route("/student/profile/{id}", name="my_student_profile")
     */
    public function showProfile($id, Request $request, EntityManagerInterface $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Student::class);

        $student = $repo->find($id);

        $studentid = $repo->find($id)->getId();
        dump($studentid);

        $userid = $this->getUser()->getId();
        dump($userid);

        // SECURISATION URL
        if ($userid != $studentid) {
            return $this->redirectToRoute('homepage');
        }


        return $this->render(
            'student/profile.html.twig',
            [
                'student' => $student
            ]
        );
    }

    /**
     * @Route("/student/edit/{id}", name="edit_my_student_profile")
     */
    public function edit(Student $student, Request $request, SluggerInterface $slugger, EntityManagerInterface $manager)
    {

        $userid = $this->getUser()->getId();
        dump($userid); // id du student connecté

        $urlid = $request->attributes->get('id');
        dump($urlid); // id dans l'url

        // SECURISATION URL
        if ($userid != $urlid) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(EditStudentType::class, $student);

        $form->handleRequest($request);
        dump($student);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newPhotoFile = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

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
            dump('test');

            $sport = $request->request->get('sport');
            $student->setSport($sport);

            $music = $request->request->get('music');
            $student->setMusic($music);

            $setOtherHobbies = $request->request->get('OtherHobbies');
            $student->setOtherHobbies($setOtherHobbies);

            $student->setRoles(['ROLE_STUDENT']);

            $manager->persist($student);
            $manager->flush();

            return $this->redirectToRoute('my_student_profile', ['id' => $student->getId()]);
        }

        return $this->render(
            'student/edit.html.twig',
            [
                'formEditStudent' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/student/my_partners", name="student_partners")
     */
    public function showPartners(StudentDuoRepository $studentDuoRepo, StudentRepository $studentRepo)
    {
        $userid = $this->getUser()->getId();
        dump($userid);

        $myDuos = $studentDuoRepo->findDuoByStudent($userid);
        dump($myDuos);

        $students = $studentRepo->findAll();
        dump($students);

        return $this->render('student/student_partners.html.twig', [
            'myDuos' => $myDuos,
            'students' => $students,
            'userid' => $userid
        ]);
    }

    /**
     * @Route("/student/my_exchanges/{id}", name="student_exchanges")
     */
    public function showExchanges(Request $request, StudentDuoRepository $studentDuoRepo)
    {

        $userid = $this->getUser()->getId();
        dump($userid); // id de l'utilisateur

        $totalDuos = $studentDuoRepo->findAll();
        dump($totalDuos); // on trouve le duo de l'url

        $urlid = $request->attributes->get('id');
        dump($urlid); // id dans l'url, càd id du student duo


        // Si l'id entré dans l'URL est supérieur aux nombre possible de duos, ça redirige
        if ($urlid > count($totalDuos)) {
            return $this->redirectToRoute('student_partners');
        }

        $urlDuo = $studentDuoRepo->findById($urlid);
        dump($urlDuo); // on trouve le duo de l'url

        foreach ($urlDuo as $student1duo) {
            $student1 = $student1duo->getStudent1();
        }
        dump($student1); // on le student 1 du duo

        foreach ($urlDuo as $student2duo) {
            $student2 = $student2duo->getStudent2();
        }
        dump($student2); // on le student 2 du duo


        // Si l'id de l'utilisateur ne correspond pas à 1 des étudiants du duo de l'URL 
        if ($userid != $student1 && $userid != $student2) {
            // return $this->redirectToRoute('student_partners');
            echo ("prout");
        }

        return $this->render('student/student_exchanges.html.twig');
    }
}
