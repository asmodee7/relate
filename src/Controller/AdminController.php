<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("admin/new-school", name="create_school")
     */
    public function newSchool(Request $request, EntityManagerInterface $manager): Response
    {
        $school = new School;

        $schoolForm = $this->createForm(SchoolType::class, $school);

        $schoolForm->handleRequest($request);

        dump($request);

        if ($schoolForm->isSubmitted() && $schoolForm->isValid()) {
            $manager->persist($school);
            $manager->flush();

            $this->redirectToRoute("create_school");
        }

        return $this->render("admin/create_school.html.twig", [
            'schoolForm' => $schoolForm->createView()
        ]);
    }
}
