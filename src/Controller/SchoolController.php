<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Contact;
use App\Entity\Teacher;
use App\Form\ContactType;
use App\Form\TeacherType;
use App\Form\EditSchoolType;
use App\Repository\SchoolRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Notification\ContactNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SchoolController extends AbstractController
{
    /**
     * @Route("school/new-teacher", name="new_teacher")
     */
    public function newSchoolTeacher(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $teacher = new Teacher;

        $newTeacherForm = $this->createForm(TeacherType::class, $teacher);

        $newTeacherForm->handleRequest($request);

        dump($request);


        if ($newTeacherForm->isSubmitted() && $newTeacherForm->isValid()) {
            $photoFile = $newTeacherForm->get('photo')->getData();
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
                $teacher->setPhoto($newPhotoFile);
            }


            $hash = $encoder->encodePassword($teacher, $teacher->getPassword());
            $teacher->setPassword($hash);

            $teacher->setIdSchool($this->getUser());

            $teacher->setRoles(["ROLE_TEACHER"]);
            $manager->persist($teacher);
            $manager->flush();

            return $this->redirectToRoute("teachers");
        }

        return $this->render("school/create_teacher.html.twig", [
            'newTeacherForm' => $newTeacherForm->createView()
        ]);
    }

    /**
     * @Route("school/teachers", name="teachers")
     */
    public function showTeachers(TeacherRepository $repo)
    {
        $userid = $this->getUser()->getId();

        // foreach ($teacherliste as $teachers) {

        //     $teacherliste = $repo->findById($userid);
        //     dump($teacherliste);

        // }

        $teachers = $repo->findTeacherBySchool($userid);

        dump($userid);
        dump($teachers);

        return $this->render("school/teachers.html.twig", [
            'teachers' => $teachers
        ]);
    }

    /**
     * @Route("/school/teacher/infos/{id}", name="my_teacher_infos")
     */
    public function showTeacherInfos(TeacherRepository $repo, $id)
    {

        $teacher = $repo->find($id);

        // SECURISATION URL
        $teacherid = $repo->find($id)->getIdSchool()->getId();
        dump($teacherid); // on récupère l'id_school du teacher dans l'URL

        $userid = $this->getUser()->getId(); // id de la school connectée
        dump($userid);

        if ($teacherid != $userid) {
            return $this->redirectToRoute("homepage");
        }


        return $this->render(
            'school/teacherinfos.html.twig',
            [
                'teacher' => $teacher
            ]
        );
    }

    /**
     * @Route("/school/infos/{id}", name="my_school_infos")
     */
    public function showInfos(SchoolRepository $repo, $id)
    {
        $school = $repo->find($id);

        $schoolid = $repo->find($id)->getId();
        dump($schoolid);

        $userid = $this->getUser()->getId(); // id de la school connectée
        dump($userid);

        // SECURISATION URL

        if ($schoolid != $userid) {
            return $this->redirectToRoute("homepage");
        }


        return $this->render(
            'school/infos.html.twig',
            [
                'school' => $school
            ]
        );
    }

    /**
     * @Route("/school/edit/{id}", name="edit_my_school_infos")
     */
    public function edit(School $school, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        $userid = $this->getUser()->getId();
        dump($userid); // id de l'école connectée

        $urlid = $request->attributes->get('id');
        dump($urlid);

        // SECURISATION URL
        if ($userid != $urlid) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(EditSchoolType::class, $school);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($school, $school->getPassword());
            $school->setPassword($hash);

            $manager->persist($school);
            $manager->flush();

            return $this->redirectToRoute('my_school_infos', ['id' => $school->getId()]);
        }

        return $this->render(
            'school/edit.html.twig',
            [
                'formEditSchool' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/school/contact", name="school_contact")
     */
    public function contact(Request $request, EntityManagerInterface $manager, ContactNotification $notification)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Your email has been sent');
            $manager->persist($contact); // on prépare l'insertion $manager->flush(); // on execute l'insertion

            return $this->redirectToRoute('school_contact');
        }
        return $this->render("school/contact.html.twig", ['formContact' => $form->createView()]);
    }
}
