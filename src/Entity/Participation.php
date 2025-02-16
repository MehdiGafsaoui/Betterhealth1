<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateparticipation = null;

    #[ORM\Column]
    private ?int $nombrepersonne = null;

    #[ORM\ManyToOne(inversedBy: 'participations', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $participant = null;

    #[ORM\ManyToOne(targetEntity: Evenements::class)]
    #[ORM\JoinColumn(name: 'evenement_id', referencedColumnName: 'id', nullable: false)]
    private ?Evenements $evenement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateparticipation(): ?\DateTimeInterface
    {
        return $this->dateparticipation;
    }

    public function setDateparticipation(\DateTimeInterface $dateparticipation): static
    {
        $this->dateparticipation = $dateparticipation;
        return $this;
    }

    public function getNombrepersonne(): ?int
    {
        return $this->nombrepersonne;
    }

    public function setNombrepersonne(int $nombrepersonne): static
    {
        $this->nombrepersonne = $nombrepersonne;
        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): static
    {
        $this->participant = $participant;
        return $this;
    }

    public function getEvenement(): ?Evenements
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenements $evenement): static
    {
        $this->evenement = $evenement;
        return $this;
    }
}
