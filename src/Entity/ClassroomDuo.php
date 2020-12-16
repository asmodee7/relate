<?php

namespace App\Entity;

use App\Repository\ClassroomDuoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ClassroomDuoRepository::class)
 */
class ClassroomDuo
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
    private $classroom_1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $classroom_2;

    /**
     * @ORM\ManyToMany(targetEntity=Classroom::class, inversedBy="classroomDuos")
     */
    private $classrooms;

    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassroom1(): ?string
    {
        return $this->classroom_1;
    }

    public function setClassroom1(string $classroom_1): self
    {
        $this->classroom_1 = $classroom_1;

        return $this;
    }

    public function getClassroom2(): ?string
    {
        return $this->classroom_2;
    }

    public function setClassroom2(string $classroom_2): self
    {
        $this->classroom_2 = $classroom_2;

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
        }

        return $this;
    }

    public function removeClassroom(Classroom $classroom): self
    {
        $this->classrooms->removeElement($classroom);

        return $this;
    }
}
