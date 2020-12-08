<?php

namespace App\Entity;

use App\Repository\StudentDuoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentDuoRepository::class)
 */
class StudentDuo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $student_1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $student_2;

    /**
     * @ORM\ManyToMany(targetEntity=Student::class, inversedBy="studentDuos")
     */
    private $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent1(): ?int
    {
        return $this->student_1;
    }

    public function setStudent1(int $student_1): self
    {
        $this->student_1 = $student_1;

        return $this;
    }

    public function getStudent2(): ?string
    {
        return $this->student_2;
    }

    public function setStudent2(string $student_2): self
    {
        $this->student_2 = $student_2;

        return $this;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        $this->students->removeElement($student);

        return $this;
    }
}
