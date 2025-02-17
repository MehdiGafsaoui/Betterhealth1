<?php

namespace App\Entity;

use App\Repository\TherapieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TherapieRepository::class)]
class Therapie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    #[Assert\Length(min: 3, minMessage: 'Le nom doit comporter au moins 3 caractères')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 10, minMessage: 'La description doit comporter au moins 10 caractères')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'objectif est obligatoire.')]
    #[Assert\Length(min: 5, minMessage: 'L\'objectif doit comporter au moins 5 caractères')]
    private ?string $objectif = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La durée est obligatoire.')]
    #[Assert\Regex(pattern: '/^\d+$/', message: 'La durée doit être un nombre entier positif')]
    private ?string $duree = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type est obligatoire.')]
    private ?string $type = null;

    /**
     * @var Collection<int, ExerciceMental>
     */
    #[ORM\OneToMany(targetEntity: ExerciceMental::class, mappedBy: 'therapie')]
    private Collection $exerciceMentals;

    public function __construct()
    {
        $this->exerciceMentals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
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

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): static
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, ExerciceMental>
     */
    public function getExerciceMentals(): Collection
    {
        return $this->exerciceMentals;
    }

    public function addExerciceMental(ExerciceMental $exerciceMental): static
    {
        if (!$this->exerciceMentals->contains($exerciceMental)) {
            $this->exerciceMentals->add($exerciceMental);
            $exerciceMental->setTherapie($this);
        }

        return $this;
    }

    public function removeExerciceMental(ExerciceMental $exerciceMental): static
    {
        if ($this->exerciceMentals->removeElement($exerciceMental)) {
            // set the owning side to null (unless already changed)
            if ($exerciceMental->getTherapie() === $this) {
                $exerciceMental->setTherapie(null);
            }
        }

        return $this;
    }
}
