<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("teacher/new-teacher", name="create_teacher")
     */
    public function newTeacher(Request $request, EntityManagerInterface $manager): Response
    {
        $teacher = new Teacher;

        $teacherForm = $this->createForm(TeacherType::class, $teacher);

        $teacherForm->handleRequest($request);

        dump($request);

        if ($teacherForm->isSubmitted() && $teacherForm->isValid()) {
            $manager->persist($teacher);
            $manager->flush();

            $this->redirectToRoute("create_teacher");
        }

        return $this->render("teacher/create_teacher.html.twig", [
            'teacherForm' => $teacherForm->createView()
        ]);
    }
}
