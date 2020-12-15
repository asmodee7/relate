<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Form\EditSchoolType;
use App\Repository\SchoolRepository;
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
     * @Route("/school/infos/{id}", name="my_school_infos")
     */
    public function showInfos(SchoolRepository $repo, $id)
    {
        $school = $repo->find($id);

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
