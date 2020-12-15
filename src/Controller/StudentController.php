<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\EditStudentType;
use App\Form\LoginStudentType;
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
    public function edit(Student $student, Request $request, SluggerInterface $slugger, EntityManagerInterface $manager)
    {


        $form = $this->createForm(EditStudentType::class, $student);

        $form->handleRequest($request);
        dump($student);

        if($form->isSubmitted() && $form->isValid())
        {
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
                $student->setPhoto($newPhotoFile);
            }
            dump('test');
            $student->setRoles(['ROLE_STUDENT']);

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