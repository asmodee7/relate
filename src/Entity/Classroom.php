<?php

namespace App\Entity;

use App\Repository\ClassroomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClassroomRepository::class)
 */
class Classroom
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Teacher::class, inversedBy="classrooms")
     */
    private $teachers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $grade;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $classroom_name;

    /**
     * @ORM\ManyToMany(targetEntity=Student::class, mappedBy="classrooms")
     */
    private $students;

    /**
     * @ORM\ManyToMany(targetEntity=ClassroomDuo::class, mappedBy="classrooms")
     */
    private $classroomDuos;

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->classroomDuos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        }

        return $this;
    }

    // public function setTeacher(Teacher $teacher): self
    // {
    //     $this->teacher = $teacher;

    //     return $this;
    // }

    public function removeTeacher(Teacher $teacher): self
    {
        $this->teachers->removeElement($teacher);

        return $this;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(string $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getclassroom_name(): ?string
    {
        return $this->classroom_name;
    }

    public function setclassroom_name(string $classroom_name): self
    {
        $this->classroom_name = $classroom_name;

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
            $student->addClassroom($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            $student->removeClassroom($this);
        }

        return $this;
    }

    /**
     * @return Collection|ClassroomDuo[]
     */
    public function getClassroomDuos(): Collection
    {
        return $this->classroomDuos;
    }

    public function addClassroomDuo(ClassroomDuo $classroomDuo): self
    {
        if (!$this->classroomDuos->contains($classroomDuo)) {
            $this->classroomDuos[] = $classroomDuo;
            $classroomDuo->addClassroom($this);
        }

        return $this;
    }

    public function removeClassroomDuo(ClassroomDuo $classroomDuo): self
    {
        if ($this->classroomDuos->removeElement($classroomDuo)) {
            $classroomDuo->removeClassroom($this);
        }

        return $this;
    }
}
