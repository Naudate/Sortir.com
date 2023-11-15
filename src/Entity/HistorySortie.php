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

    #[ORM\Column]
    private ?int $sortie_id = null;

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
    private ?string $etat = null;

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

    #[ORM\Column]
    private ?int $site_id = null;

    #[ORM\Column(length: 255)]
    private ?string $site_nom = null;

    #[ORM\Column]
    private ?int $organisateur_id = null;

    #[ORM\Column(length: 255)]
    private ?string $organisateur_nom = null;

    #[ORM\Column(length: 255)]
    private ?string $organisateur_prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $organisateur_pseudo = null;

    #[ORM\Column(length: 255)]
    private ?string $organisateur_mail = null;

    #[ORM\Column]
    private array $organisateur_roles = [];

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $organisateur_telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $organisateur_photo = null;

    #[ORM\Column]
    private ?bool $organisateur_is_actif = null;

    #[ORM\Column(nullable: true)]
    private ?bool $organisateur_first_connection = null;

    #[ORM\Column]
    private ?bool $organisateur_is_change_password = null;

    #[ORM\Column]
    private ?int $organisateur_site_id = null;

    #[ORM\Column(length: 255)]
    private ?string $organisateur_site_nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $updatedBy_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedBy_nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedBy_prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedBy_pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedBy_email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedBy_photo = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $updatedBy_telephone = null;

    #[ORM\Column(nullable: true)]
    private ?array $updatedBy_roles = null;

    #[ORM\Column(nullable: true)]
    private ?bool $updatedBy_first_connection = null;

    #[ORM\Column(nullable: true)]
    private ?bool $updatedBy_is_change_password = null;

    #[ORM\Column(nullable: true)]
    private ?bool $updatedBy_is_actif = null;

    #[ORM\Column(nullable: true)]
    private ?int $updatedBy_site_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updatedBy_site_nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_modification = null;

    #[ORM\Column(nullable: true)]
    private ?array $participants = null;



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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

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

    public function getSiteId(): ?int
    {
        return $this->site_id;
    }

    public function setSiteId(int $site_id): static
    {
        $this->site_id = $site_id;

        return $this;
    }

    public function getSiteNom(): ?string
    {
        return $this->site_nom;
    }

    public function setSiteNom(string $site_nom): static
    {
        $this->site_nom = $site_nom;

        return $this;
    }

    public function getOrganisateurId(): ?int
    {
        return $this->organisateur_id;
    }

    public function setOrganisateurId(int $organisateur_id): static
    {
        $this->organisateur_id = $organisateur_id;

        return $this;
    }

    public function getOrganisateurNom(): ?string
    {
        return $this->organisateur_nom;
    }

    public function setOrganisateurNom(string $organisateur_nom): static
    {
        $this->organisateur_nom = $organisateur_nom;

        return $this;
    }

    public function getOrganisateurPrenom(): ?string
    {
        return $this->organisateur_prenom;
    }

    public function setOrganisateurPrenom(string $organisateur_prenom): static
    {
        $this->organisateur_prenom = $organisateur_prenom;

        return $this;
    }

    public function getOrganisateurPseudo(): ?string
    {
        return $this->organisateur_pseudo;
    }

    public function setOrganisateurPseudo(string $organisateur_pseudo): static
    {
        $this->organisateur_pseudo = $organisateur_pseudo;

        return $this;
    }

    public function getOrganisateurMail(): ?string
    {
        return $this->organisateur_mail;
    }

    public function setOrganisateurMail(string $organisateur_mail): static
    {
        $this->organisateur_mail = $organisateur_mail;

        return $this;
    }

    public function getOrganisateurRoles(): array
    {
        return $this->organisateur_roles;
    }

    public function setOrganisateurRoles(array $organisateur_roles): static
    {
        $this->organisateur_roles = $organisateur_roles;

        return $this;
    }

    public function getOrganisateurTelephone(): ?string
    {
        return $this->organisateur_telephone;
    }

    public function setOrganisateurTelephone(?string $organisateur_telephone): static
    {
        $this->organisateur_telephone = $organisateur_telephone;

        return $this;
    }

    public function getOrganisateurPhoto(): ?string
    {
        return $this->organisateur_photo;
    }

    public function setOrganisateurPhoto(?string $organisateur_photo): static
    {
        $this->organisateur_photo = $organisateur_photo;

        return $this;
    }

    public function isOrganisateurIsActif(): ?bool
    {
        return $this->organisateur_is_actif;
    }

    public function setOrganisateurIsActif(bool $organisateur_is_actif): static
    {
        $this->organisateur_is_actif = $organisateur_is_actif;

        return $this;
    }

    public function isOrganisateurFirstConnection(): ?bool
    {
        return $this->organisateur_first_connection;
    }

    public function setOrganisateurFirstConnection(?bool $organisateur_first_connection): static
    {
        $this->organisateur_first_connection = $organisateur_first_connection;

        return $this;
    }

    public function isOrganisateurIsChangePassword(): ?bool
    {
        return $this->organisateur_is_change_password;
    }

    public function setOrganisateurIsChangePassword(bool $organisateur_is_change_password): static
    {
        $this->organisateur_is_change_password = $organisateur_is_change_password;

        return $this;
    }

    public function getOrganisateurSiteId(): ?int
    {
        return $this->organisateur_site_id;
    }

    public function setOrganisateurSiteId(int $organisateur_site_id): static
    {
        $this->organisateur_site_id = $organisateur_site_id;

        return $this;
    }

    public function getOrganisateurSiteNom(): ?string
    {
        return $this->organisateur_site_nom;
    }

    public function setOrganisateurSiteNom(string $organisateur_site_nom): static
    {
        $this->organisateur_site_nom = $organisateur_site_nom;

        return $this;
    }

    public function getUpdatedById(): ?int
    {
        return $this->updatedBy_id;
    }

    public function setUpdatedById(int $updatedBy_id): static
    {
        $this->updatedBy_id = $updatedBy_id;

        return $this;
    }

    public function getUpdatedByNom(): ?string
    {
        return $this->updatedBy_nom;
    }

    public function setUpdatedByNom(?string $updatedBy_nom): static
    {
        $this->updatedBy_nom = $updatedBy_nom;

        return $this;
    }

    public function getUpdatedByPrenom(): ?string
    {
        return $this->updatedBy_prenom;
    }

    public function setUpdatedByPrenom(?string $updatedBy_prenom): static
    {
        $this->updatedBy_prenom = $updatedBy_prenom;

        return $this;
    }

    public function getUpdatedByPseudo(): ?string
    {
        return $this->updatedBy_pseudo;
    }

    public function setUpdatedByPseudo(?string $updatedBy_pseudo): static
    {
        $this->updatedBy_pseudo = $updatedBy_pseudo;

        return $this;
    }

    public function getUpdatedByEmail(): ?string
    {
        return $this->updatedBy_email;
    }

    public function setUpdatedByEmail(?string $updatedBy_email): static
    {
        $this->updatedBy_email = $updatedBy_email;

        return $this;
    }

    public function getUpdatedByPhoto(): ?string
    {
        return $this->updatedBy_photo;
    }

    public function setUpdatedByPhoto(?string $updatedBy_photo): static
    {
        $this->updatedBy_photo = $updatedBy_photo;

        return $this;
    }

    public function getUpdatedByTelephone(): ?string
    {
        return $this->updatedBy_telephone;
    }

    public function setUpdatedByTelephone(?string $updatedBy_telephone): static
    {
        $this->updatedBy_telephone = $updatedBy_telephone;

        return $this;
    }

    public function getUpdatedByRoles(): ?array
    {
        return $this->updatedBy_roles;
    }

    public function setUpdatedByRoles(?array $updatedBy_roles): static
    {
        $this->updatedBy_roles = $updatedBy_roles;

        return $this;
    }

    public function isUpdatedByFirstConnection(): ?bool
    {
        return $this->updatedBy_first_connection;
    }

    public function setUpdatedByFirstConnection(?bool $updatedBy_first_connection): static
    {
        $this->updatedBy_first_connection = $updatedBy_first_connection;

        return $this;
    }

    public function isUpdatedByIsChangePassword(): ?bool
    {
        return $this->updatedBy_is_change_password;
    }

    public function setUpdatedByIsChangePassword(?bool $updatedBy_is_change_password): static
    {
        $this->updatedBy_is_change_password = $updatedBy_is_change_password;

        return $this;
    }

    public function isUpdatedByIsActif(): ?bool
    {
        return $this->updatedBy_is_actif;
    }

    public function setUpdatedByIsActif(?bool $updatedBy_is_actif): static
    {
        $this->updatedBy_is_actif = $updatedBy_is_actif;

        return $this;
    }

    public function getUpdatedBySiteId(): ?int
    {
        return $this->updatedBy_site_id;
    }

    public function setUpdatedBySiteId(?int $updatedBy_site_id): static
    {
        $this->updatedBy_site_id = $updatedBy_site_id;

        return $this;
    }

    public function getUpdatedBySiteNom(): ?string
    {
        return $this->updatedBy_site_nom;
    }

    public function setUpdatedBySiteNom(?string $updatedBy_site_nom): static
    {
        $this->updatedBy_site_nom = $updatedBy_site_nom;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(?\DateTimeInterface $date_modification): static
    {
        $this->date_modification = $date_modification;

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
