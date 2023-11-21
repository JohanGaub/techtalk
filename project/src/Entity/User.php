<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $LastName = null;

    #[ORM\Column(nullable: false)]
    private ?bool $isEnabled = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): self {
        $this->LastName = $LastName;

        return $this;
    }

    public function isIsEnabled(): ?bool {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): self {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getAgency(): ?Agency {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self {
        $this->agency = $agency;

        return $this;
    }
}
