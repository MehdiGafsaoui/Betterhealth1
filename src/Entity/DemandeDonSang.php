<?php

namespace App\Entity;

use App\Repository\DemandeDonSangRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DemandeDonSangRepository::class)]
class DemandeDonSang
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le groupe sanguin est requis.")]
    #[Assert\Choice(choices: ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"], message: "Groupe sanguin invalide.")]
    private ?string $groupesanguain = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "La quantité est requise.")]
    #[Assert\Positive(message: "La quantité doit être un nombre positif.")]
    private ?float $quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;
    

    #[ORM\ManyToOne]
    #[Assert\NotNull(message: "L'utilisateur est requis.")]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull(message: "Le centre de don est requis.")]
    private ?CentreDeDon $CentreDeDon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupesanguain(): ?string
    {
        return $this->groupesanguain;
    }

    public function setGroupesanguain(string $groupesanguain): static
    {
        $this->groupesanguain = $groupesanguain;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCentreDeDon(): ?CentreDeDon
    {
        return $this->CentreDeDon;
    }

    public function setCentreDeDon(?CentreDeDon $CentreDeDon): static
    {
        $this->CentreDeDon = $CentreDeDon;

        return $this;
    }
}