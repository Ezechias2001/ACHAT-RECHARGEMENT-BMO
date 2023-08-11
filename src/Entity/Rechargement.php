<?php

namespace App\Entity;

use App\Repository\RechargementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RechargementRepository::class)]
class Rechargement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $NumeroCarte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCarte = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?int $commission = null;
    
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?HistoriqueCommission $relation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getNumeroCarte(): ?int
    {
        return $this->NumeroCarte;
    }

    public function setNumeroCarte(?int $NumeroCarte): static
    {
        $this->NumeroCarte = $NumeroCarte;

        return $this;
    }

    public function getTypeCarte(): ?string
    {
        return $this->typeCarte;
    }

    public function setTypeCarte(?string $typeCarte): static
    {
        $this->typeCarte = $typeCarte;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateCreation(): ?string
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?string $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCommission(): ?int
    {
        return $this->commission;
    }

    public function setCommission(?int $commission): static
    {
        $this->commission = $commission;

        return $this;
    }
    public function getRelation(): ?HistoriqueCommission
    {
        return $this->relation;
    }

    public function setRelation(?HistoriqueCommission $relation): static
    {
        $this->relation = $relation;

        return $this;
    }
}
