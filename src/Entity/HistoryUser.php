<?php

namespace App\Entity;

use App\Repository\HistoryUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryUserRepository::class)]
class HistoryUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $user = [];

    #[ORM\Column(nullable: true)]
    private ?array $sortie = null;

    #[ORM\Column(nullable: true)]
    private ?array $participants = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function setUser(array $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSortie(): ?array
    {
        return $this->sortie;
    }

    public function setSortie(?array $sortie): static
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getParticipants(): ?array
    {
        return $this->participants;
    }

    public function setParticipants(?array $participants): static
    {
        $this->participants = $participants;

        return $this;
    }
}
