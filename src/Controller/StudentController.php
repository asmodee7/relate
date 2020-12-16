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
    public function showPartners(StudentDuoRepository $studentDuoRepo)
    {
        $userid = $this->getUser()->getId();
        dump($userid);

        $myDuos = $studentDuoRepo->findDuoByStudent($userid);
        dump($myDuos);
        return $this->render('student/student_partners.html.twig', [
            'myDuos' => $myDuos
        ]);
    }

    /**
     * @Route("/student/my_exchanges/{id}", name="student_exchanges")
     */
    public function showExchanges()
    {
        return $this->render('student/student_exchanges.html.twig');
    }
}