<?php

namespace App\Controller;

use App\Entity\Teacher;
use App\Form\TeacherType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends AbstractController
{
    // /**
    //  * @Route("/security", name="security")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('security/index.html.twig', [
    //         'controller_name' => 'SecurityController',
    //     ]);
    // }

    // /**
    //  * @Route("/inscription-teacher", name="registration_teacher")
    //  */
    // public function registration(Request $request)
    // {
    //     $teacher = new Teacher;

    //     $formRegistrationTeacher = $this->createForm(TeacherType::class, $teacher);

    //     $formRegistrationTeacher->handleRequest($request);
        
    //     return $this->render('security/registration_teacher.html.twig');
    // }

    /**
     * @Route("/", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_ADMIN') || $authChecker->isGranted('ROLE_SCHOOL') || $authChecker->isGranted('ROLE_TEACHER') || $authChecker->isGranted('ROLE_STUDENT')) {
            return $this->redirectToRoute("homepage");
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("\logout", name="logout")
     */
    public function logout()
    {
        
    }

}
