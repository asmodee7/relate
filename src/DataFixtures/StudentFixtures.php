<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StudentFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i < 10; $i++)
        {
            $student = new Student();
            $student->setFirstname("Prenom $i");
            $student->setLastname("Nom $i");
            $student->setAge("13");
            $student->setDescription("Description $i");
            $student->setSport("Sport $i");
            $student->setMusic("Music $i");
            $student->setOtherHobbies("Other Hobbies $i");
            $student->setUsername("user$i");

            $password = $this->encoder->encodePassword($student, 'azerty');
            $student->setPassword("$password");

/*             $student->getClassrooms("");
            $student->getStudentDuos("");
            $student->setRoles(""); */

            $manager->persist($student);
        }

        $manager->flush();
    }
}
