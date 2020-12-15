<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Form\EditSchoolType;
use App\Repository\SchoolRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SchoolController extends AbstractController
{
    /**
     * @Route("/school", name="school")
     */
    public function index(): Response
    {
        return $this->render('school/index.html.twig', [
            'controller_name' => 'SchoolController',
        ]);
    }
    /**
     * @Route("school/new-teacher", name="new_teacher")
     */
    public function newSchoolTeacher(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $teacher = new Teacher;

        $newTeacherForm = $this->createForm(TeacherType::class, $teacher);

        $newTeacherForm->handleRequest($request);

        dump($request);

        if ($newTeacherForm->isSubmitted() && $newTeacherForm->isValid()) {
            $hash = $encoder->encodePassword($teacher, $teacher->getPassword());
            $teacher->setPassword($hash);

            $teacher->setIdSchool($this->getUser());

            $teacher->setRoles(["ROLE_TEACHER"]);
            $manager->persist($teacher);
            $manager->flush();

            $this->redirectToRoute("new_teacher");
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
        dump($teacher);

        // $teacherid = $repo->find($id)->getId();
        // dump($teacherid);

        $userid = $this->getUser(); // id de la school connectée
        dump($userid);

        // if ($teacherid != $userid) {
        //     return $this->redirectToRoute("homepage");
        // }


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
    public function edit(School $school, Request $request, EntityManagerInterface $manager)
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
}
