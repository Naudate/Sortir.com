<?php

namespace App\Entity;

use App\Repository\HistorySortieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistorySortieRepository::class)]
class HistorySortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_heure_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_heure_fin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_limite_inscription = null;

    #[ORM\Column]
    private ?int $nombre_max_participant = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $is_publish = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 255)]
    private ?string $organisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(length: 255)]
    private ?string $site = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_nom = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_rue = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu_ville_nom = null;

    #[ORM\Column(length: 5)]
    private ?string $lieu_ville_code_postal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu_longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu_latitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $update_by = null;

    #[ORM\Column]
    private ?int $sortie_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->date_heure_debut;
    }

    public function setDateHeureDebut(\DateTimeInterface $date_heure_debut): static
    {
        $this->date_heure_debut = $date_heure_debut;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->date_heure_fin;
    }

    public function setDateHeureFin(\DateTimeInterface $date_heure_fin): static
    {
        $this->date_heure_fin = $date_heure_fin;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->date_limite_inscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $date_limite_inscription): static
    {
        $this->date_limite_inscription = $date_limite_inscription;

        return $this;
    }

    public function getNombreMaxParticipant(): ?int
    {
        return $this->nombre_max_participant;
    }

    public function setNombreMaxParticipant(int $nombre_max_participant): static
    {
        $this->nombre_max_participant = $nombre_max_participant;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsPublish(): ?bool
    {
        return $this->is_publish;
    }

    public function setIsPublish(bool $is_publish): static
    {
        $this->is_publish = $is_publish;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getOrganisateur(): ?string
    {
        return $this->organisateur;
    }

    public function setOrganisateur(string $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getLieuNom(): ?string
    {
        return $this->lieu_nom;
    }

    public function setLieuNom(string $lieu_nom): static
    {
        $this->lieu_nom = $lieu_nom;

        return $this;
    }

    public function getLieuRue(): ?string
    {
        return $this->lieu_rue;
    }

    public function setLieuRue(string $lieu_rue): static
    {
        $this->lieu_rue = $lieu_rue;

        return $this;
    }

    public function getLieuVilleNom(): ?string
    {
        return $this->lieu_ville_nom;
    }

    public function setLieuVilleNom(string $lieu_ville_nom): static
    {
        $this->lieu_ville_nom = $lieu_ville_nom;

        return $this;
    }

    public function getLieuVilleCodePostal(): ?string
    {
        return $this->lieu_ville_code_postal;
    }

    public function setLieuVilleCodePostal(string $lieu_ville_code_postal): static
    {
        $this->lieu_ville_code_postal = $lieu_ville_code_postal;

        return $this;
    }

    public function getLieuLongitude(): ?string
    {
        return $this->lieu_longitude;
    }

    public function setLieuLongitude(?string $lieu_longitude): static
    {
        $this->lieu_longitude = $lieu_longitude;

        return $this;
    }

    public function getLieuLatitude(): ?string
    {
        return $this->lieu_latitude;
    }

    public function setLieuLatitude(?string $lieu_latitude): static
    {
        $this->lieu_latitude = $lieu_latitude;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getUpdateBy(): ?string
    {
        return $this->update_by;
    }

    public function setUpdateBy(?string $update_by): static
    {
        $this->update_by = $update_by;

        return $this;
    }

    public function getSortieId(): ?int
    {
        return $this->sortie_id;
    }

    public function setSortieId(int $sortie_id): static
    {
        $this->sortie_id = $sortie_id;

        return $this;
    }
}
