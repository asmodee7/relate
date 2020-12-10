<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SuperadminController extends AbstractController
{
    /**
     * @Route("/superadmin", name="superadmin")
     */
    public function index(): Response
    {
        return $this->render('superadmin/index.html.twig', [
            'controller_name' => 'SuperadminController',
        ]);
    }

    /**
     * @Route("superadmin/new_admin", name="create_admin")
     */
    public function newSchool(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $admin = new Admin;

        $adminForm = $this->createForm(AdminType::class, $admin);

        $adminForm->handleRequest($request);

        dump($request);

        if ($adminForm->isSubmitted() && $adminForm->isValid()) {

            $hash = $encoder->encodePassword($admin, $admin->getPassword());

            $admin->setPassword($hash);

            $manager->persist($admin);
            $manager->flush();

            $this->redirectToRoute("create_school");
        }

        return $this->render("superadmin/create_admin.html.twig", [
            'adminForm' => $adminForm->createView()
        ]);
    }
}