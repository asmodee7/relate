<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=TeacherRepository::class)
 * @UniqueEntity(
 *      fields = {"email"},
 *      message="E-mail already exist"
 * )
 * @UniqueEntity(
 *      fields = {"username"},
 *      message="Username already exist"
 * )
 */
class Teacher implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="teachers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_school;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a username")
     * @Assert\Length(
     *      min="4",
     *      minMessage="At least 4 characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(                       
     *        min="8",
     *        minMessage="At least 8 characters",
     * )
     * @Assert\EqualTo(
     *      propertyPath="confirm_password",
     *      message="Passwords don't match",
     *      groups={"create_teacher"} 
     * )
     */
    private $password;

    /**
     * @Assert\EqualTo(
     *      propertyPath="password",
     *      message="Passwords don't match",
     *      groups={"create_teacher"} 
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
     * @ORM\Column(type="string", length=255)
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Classroom::class, mappedBy="teachers")
     */
    private $classrooms;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSchool(): ?School
    {
        return $this->id_school;
    }

    public function setIdSchool(?School $id_school): self
    {
        $this->id_school = $id_school;

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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?int $phone_number): self
    {
        $this->phone_number = $phone_number;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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
            $classroom->addTeacher($this);
        }

        return $this;
    }

    public function removeClassroom(Classroom $classroom): self
    {
        if ($this->classrooms->removeElement($classroom)) {
            $classroom->removeTeacher($this);
        }

        return $this;
    }

    // Cette méthode est uniquement destinée à nettoyer les mots de passe en texte brute éventuellement stockés
    public function eraseCredentials()
    {
    }

    // Renvoie la chaine de caractère non encodée que l'utilisateur a saisi, qui est utilisé à l'origine pour encoder le mot de passe
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
