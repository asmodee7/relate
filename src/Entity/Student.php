<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 * @UniqueEntity(
 * fields = {"username"},
 * message = "This username already exists"
 * )

 */
class Student implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Classroom::class, inversedBy="students")
     */
    private $classrooms;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un nom d'utilisateur !")
     * @Assert\Length(
     *      min="2",
     *      minMessage="Votre nom d'utilisateur doit contenir minimum 2 caractères !"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(                       
     *        min="8",
     *        minMessage="Votre mot de passe doit faire minimum 8 caractères !"
     * )
     * @Assert\EqualTo(
     *      propertyPath="confirm_password",
     *      message="Les mot de passe ne correspondent pas ! Vérifiez la saisie."
     * )
     */
    private $password;

    /**
     * @Assert\EqualTo(
     *      propertyPath="password",
     *      message="Les mot de passe ne correspondent pas ! Vérifiez la saisie."
     * )
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sport;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $music;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $otherHobbies;

    /**
     * @ORM\ManyToMany(targetEntity=StudentDuo::class, mappedBy="students")
     */
    private $studentDuos;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
        $this->studentDuos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Classroom[]
     */
    public function getClassrooms(): Collection
    {
        return $this->classrooms;
    }

    public function addClassroom(Classroom $classroom): self
    {
        if (!$this->classrooms->contains($classroom)) {
            $this->classrooms[] = $classroom;
        }

        return $this;
    }

    public function removeClassroom(Classroom $classroom): self
    {
        $this->classrooms->removeElement($classroom);

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(string $sport): self
    {
        $this->sport = $sport;

        return $this;
    }

    public function getMusic(): ?string
    {
        return $this->music;
    }

    public function setMusic(string $music): self
    {
        $this->music = $music;

        return $this;
    }

    public function getOtherHobbies(): ?string
    {
        return $this->otherHobbies;
    }

    public function setOtherHobbies(string $otherHobbies): self
    {
        $this->otherHobbies = $otherHobbies;

        return $this;
    }

    /**
     * @return Collection|StudentDuo[]
     */
    public function getStudentDuos(): Collection
    {
        return $this->studentDuos;
    }

    public function addStudentDuo(StudentDuo $studentDuo): self
    {
        if (!$this->studentDuos->contains($studentDuo)) {
            $this->studentDuos[] = $studentDuo;
            $studentDuo->addStudent($this);
        }

        return $this;
    }

    public function removeStudentDuo(StudentDuo $studentDuo): self
    {
        if ($this->studentDuos->removeElement($studentDuo)) {
            $studentDuo->removeStudent($this);
        }

        return $this;
    }

    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }


    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
