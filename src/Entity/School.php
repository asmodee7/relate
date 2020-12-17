<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SchoolRepository::class)
 * @UniqueEntity(
 * fields = {"username"},
 * message = "This username already exists"
 * )
 */
class School implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a country")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a school name")
     */
    private $school_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a language")
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a username !")
     * @Assert\Length(
     *      min="4",
     *      minMessage="At least 4 characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255) 
     * @Assert\Length(                       
     *          min="8",
     *          minMessage="At least 8 characters",
     *          groups={"create_school"}
     * )
     * @Assert\EqualTo(
     *      propertyPath="confirm_password",
     *      message="Passwords don't match",
     *      groups={"create_school"}
     * )
     */
    private $password;

    /** 
     * @Assert\EqualTo(
     *      propertyPath="password",
     *      message="Passwords don't match",
     *      groups={"create_school"}
     * )
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_position;

    /**
     * @ORM\OneToMany(targetEntity=Teacher::class, mappedBy="id_school")
     */
    private $teachers;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getSchoolName(): ?string
    {
        return $this->school_name;
    }

    public function setSchoolName(string $school_name): self
    {
        $this->school_name = $school_name;

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

    public function getUserLastname(): ?string
    {
        return $this->user_lastname;
    }

    public function setUserLastname(string $user_lastname): self
    {
        $this->user_lastname = $user_lastname;

        return $this;
    }

    public function getUserFirstname(): ?string
    {
        return $this->user_firstname;
    }

    public function setUserFirstname(string $user_firstname): self
    {
        $this->user_firstname = $user_firstname;

        return $this;
    }

    public function getUserPosition(): ?string
    {
        return $this->user_position;
    }

    public function setUserPosition(string $user_position): self
    {
        $this->user_position = $user_position;

        return $this;
    }

    /**
     * @return Collection|Teacher[]
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): self
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
            $teacher->setIdSchool($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): self
    {
        if ($this->teachers->removeElement($teacher)) {
            // set the owning side to null (unless already changed)
            if ($teacher->getIdSchool() === $this) {
                $teacher->setIdSchool(null);
            }
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
