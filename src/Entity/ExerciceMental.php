<?php

namespace App\Entity;

use App\Repository\ExerciceMentalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExerciceMentalRepository::class)]
class ExerciceMental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'URL de la vidéo est obligatoire.")]
    #[Assert\Url(message: "L'URL fournie n'est pas valide.")]
    private ?string $videoUrl = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 3, minMessage: "Le titre doit comporter au moins 3 caractères.")]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 10, minMessage: "La description doit comporter au moins 10 caractères.")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La durée est obligatoire.")]
    #[Assert\Positive(message: "La durée doit être un nombre positif.")]
    #[Assert\Type(type: "integer", message: "La durée doit être un entier.")]
    private ?int $dureeMinutes = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'objectif est obligatoire.")]
    #[Assert\Length(min: 5, minMessage: "L'objectif doit comporter au moins 5 caractères.")]
    private ?string $objectif = null;


    #[ORM\ManyToOne(inversedBy: 'exerciceMentals')]
    private ?Therapie $therapie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): static
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDureeMinutes(): ?int
    {
        return $this->dureeMinutes;
    }

    public function setDureeMinutes(int $dureeMinutes): static
    {
        $this->dureeMinutes = $dureeMinutes;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): static
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function getTherapie(): ?Therapie
    {
        return $this->therapie;
    }

    public function setTherapie(?Therapie $therapie): static
    {
        $this->therapie = $therapie;

        return $this;
    }
} 