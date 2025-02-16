<?php

namespace App\Entity;

use App\Repository\EvenementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNull;


#[ORM\Entity(repositoryClass: EvenementsRepository::class)]
class Evenements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser 255 caractères.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "La date de début doit être valide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de début ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $Datedebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class, message: "La date de fin doit être valide.")]
    #[Assert\GreaterThan(propertyPath: "Datedebut", message: "La date de fin doit être après la date de début.")]
    private ?\DateTimeInterface $Datefin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu ne peut pas être vide.")]
    private ?string $Lieu = null;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Une catégorie doit être sélectionnée.")]
    private ?Categorieevenement $categorie = null;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Participation::class)]
    private Collection $participations;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: "L'image doit être une URL valide.")]
    private ?string $image = null;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->Datedebut;
    }

    public function setDatedebut(\DateTimeInterface $Datedebut): static
    {
        $this->Datedebut = $Datedebut;
        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->Datefin;
    }

    public function setDatefin(\DateTimeInterface $Datefin): static
    {
        $this->Datefin = $Datefin;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->Lieu;
    }

    public function setLieu(string $Lieu): static
    {
        $this->Lieu = $Lieu;
        return $this;
    }

    public function getCategorie(): ?Categorieevenement
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorieevenement $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
