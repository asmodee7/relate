<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use App\Form\EditSchoolType;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

            return $this->redirectToRoute("schools");
        }

        return $this->render("admin/create_school.html.twig", [
            'schoolForm' => $schoolForm->createView()
        ]);
    }

    /**
     * @Route("admin/schools", name="schools")
     */
    public function showSchools(SchoolRepository $repo)
    {
        $schools = $repo->findAll();

        return $this->render("admin/schools.html.twig", [
            'schools' => $schools
        ]);
    }

    /**
     * @Route("/admin/infos/school/{id}", name="school_infos")
     */
    public function showInfos(SchoolRepository $repo, $id)
    {
        $school = $repo->find($id);

        return $this->render(
            'admin/schoolinfos.html.twig',
            [
                'school' => $school
            ]
        );
    }

    /**
     * @Route("/admin/edit/school/{id}", name="edit_school_infos")
     */
    public function edit(SchoolRepository $repo, $id, School $school, Request $request, EntityManagerInterface $manager)
    {
        $school = $repo->find($id);

        $form = $this->createForm(EditSchoolType::class, $school);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($school);
            $manager->flush();

            return $this->redirectToRoute('school_infos', ['id' => $school->getId()]);
        }

        return $this->render(
            'admin/editschool.html.twig',
            [
                'formEditSchool' => $form->createView()
            ]
        );
    }
}
