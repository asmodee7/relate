<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    public function newSchoolTeacher(Request $request, EntityManagerInterface $manager): Response
    {
            $schoolTeacher = new Teacher;

        $schoolTeacher = $this->createForm(TeacherType:: class, $schoolTeacher);

        $schoolTeacher->handleRequest($request);

        dump($request);

        if($schoolTeacher->isSubmitted() && $schoolTeacher->isValid())
        {
            $manager->persist($schoolTeacher);
            $manager->flush();

            $this->redirectToRoute("new_teacher");
        }        

        return $this->render("school/create_teacher.html.twig", [
            'schoolTeacher' => $schoolTeacher->createView()
        ]);

    }
}
