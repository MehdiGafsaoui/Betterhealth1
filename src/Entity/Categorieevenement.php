<?php

namespace App\Entity;

use App\Repository\CategorieevenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieevenementRepository::class)]
class Categorieevenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Evenements>
     */
    #[ORM\OneToMany(targetEntity: Evenements::class, mappedBy: 'categorie')]
    private Collection $evenements;

    /**
     * @var Collection<int, evenements>
     */
    #[ORM\OneToMany(targetEntity: evenements::class, mappedBy: 'categorieevenement')]
    private Collection $evenement;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->evenement = new ArrayCollection();
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

    /**
     * @return Collection<int, Evenements>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenements $evenement): static
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setCategorie($this);
        }

        return $this;
    }

    public function removeEvenement(Evenements $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getCategorie() === $this) {
                $evenement->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, evenements>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }
}
