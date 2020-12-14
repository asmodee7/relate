<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */
class Admin implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
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
