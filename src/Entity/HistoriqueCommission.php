<?php

namespace App\Entity;

use App\Repository\HistoriqueCommissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueCommissionRepository::class)]
class HistoriqueCommission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $gainCommission = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCarte = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Operation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $datecreation = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getGainCommission(): ?int
    {
        return $this->gainCommission;
    }

    public function setGainCommission(?int $gainCommission): static
    {
        $this->gainCommission = $gainCommission;

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

    public function getOperation(): ?string
    {
        return $this->Operation;
    }

    public function setOperation(?string $Operation): static
    {
        $this->Operation = $Operation;

        return $this;
    }

    public function getDatecreation(): ?string
    {
        return $this->datecreation;
    }

    public function setDatecreation(?string $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

}
