<?php

namespace App\Entity;

use App\Repository\HistorySortieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistorySortieRepository::class)]
class HistorySortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $sortie = [];

    #[ORM\Column]
    private array $organisateur = [];

    #[ORM\Column(nullable: true)]
    private ?array $participants = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortie(): array
    {
        return $this->sortie;
    }

    public function setSortie(array $sortie): static
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getOrganisateur(): array
    {
        return $this->organisateur;
    }

    public function setOrganisateur(array $organisateur): static
    {
        $this->organisateur = $organisateur;

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
