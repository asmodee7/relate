<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{

     /**
     * @Route("/homepage", name="homepage")
     */
    public function homepage(): Response
    {
        return $this->render('admin/homepage.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("admin/new-school", name="create_school")
     */
    public function newSchool(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $school = new School;

        $schoolForm = $this->createForm(SchoolType::class, $school);

        $schoolForm->handleRequest($request);

        dump($request);

        if ($schoolForm->isSubmitted() && $schoolForm->isValid()) {

            $hash = $encoder->encodePassword($school, $school->getPassword());

            $school->setPassword($hash);

            $school->setRoles(["ROLE_SCHOOL"]);
            $manager->persist($school);
            $manager->flush();

            $this->redirectToRoute("create_school");
        }

        return $this->render("admin/create_school.html.twig", [
            'schoolForm' => $schoolForm->createView()
        ]);
    }
}
