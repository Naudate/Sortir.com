<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse mail existe déjà')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Regex('/^.+@.+\..+$/',
        message: 'Le format de l\'email n\'est pas bon')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Length(
        min: 12,
        max: 4096,
        minMessage: 'Votre mot de passe doit contenir 12 caractères'
    )]
    #[Assert\Regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/',
        message: 'Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule et 1 caractère spécial')]
    protected ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Regex('/^0([1-7]|9)\d{8}$/',
        message: 'Le numéro de téléphone doit commencer par 01 à 07 ou 09 et être composé de 10 chiffres',)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column]
    private ?bool $isActif = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Site $site = null;

    #[ORM\Column( options: ["default" => true])]
    private ?bool $firstConnection = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private Collection $sorties;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participant')]
    private Collection $inscriptions;

    #[ORM\Column]
    private ?bool $isChangePassword = null;

    #[ORM\OneToMany(mappedBy: 'updated_by', targetEntity: Sortie::class)]
    private Collection $updateSorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->updateSorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function isIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): static
    {
        $this->isActif = $isActif;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function isFirstConnection(): ?bool
    {
        return $this->firstConnection;
    }

    public function setFirstConnection(bool $firstConnection): static
    {
        $this->firstConnection = $firstConnection;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): static
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): static
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Sortie $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->addParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Sortie $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            $inscription->removeParticipant($this);
        }

        return $this;
    }

    public function isIsChangePassword(): ?bool
    {
        return $this->isChangePassword;
    }

    public function setIsChangePassword(bool $isChangePassword): static
    {
        $this->isChangePassword = $isChangePassword;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getUpdateSorties(): Collection
    {
        return $this->updateSorties;
    }

    public function addUpdateSorty(Sortie $updateSorty): static
    {
        if (!$this->updateSorties->contains($updateSorty)) {
            $this->updateSorties->add($updateSorty);
            $updateSorty->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeUpdateSorty(Sortie $updateSorty): static
    {
        if ($this->updateSorties->removeElement($updateSorty)) {
            // set the owning side to null (unless already changed)
            if ($updateSorty->getUpdatedBy() === $this) {
                $updateSorty->setUpdatedBy(null);
            }
        }

        return $this;
    }

}
