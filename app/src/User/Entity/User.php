<?php

declare(strict_types=1);

namespace App\User\Entity;

use App\User\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @var Collection<int, GroupRole>
     */
    #[ORM\ManyToMany(targetEntity: GroupRole::class)]
    private Collection $groups;

    #[ORM\Column(type: Types::STRING)]
    private string $password;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, GroupRole>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(GroupRole $group): static
    {
        if (! $this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupRole $group): static
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        // TODO: Until Groups/Roles are implemented
        if (str_contains((string) $this->email, 'admin')) {
            $roles[] = 'ROLE_ADMIN';
        }

        foreach ($this->groups as $group) {
            foreach ($group->getRoles() as $role) {
                $roles[] = $role->getName();
            }
        }

        return $roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
