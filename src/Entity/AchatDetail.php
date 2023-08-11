<?php

namespace App\Entity;

use App\Repository\AchatDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatDetailRepository::class)]
class AchatDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateNaissance = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $sexe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $piece = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroPiece = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePiece = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCarte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant = null;

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
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateNaissance(): ?string
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?string $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getPiece(): ?string
    {
        return $this->piece;
    }

    public function setPiece(?string $piece): static
    {
        $this->piece = $piece;

        return $this;
    }

    public function getNumeroPiece(): ?string
    {
        return $this->numeroPiece;
    }

    public function setNumeroPiece(?string $numeroPiece): static
    {
        $this->numeroPiece = $numeroPiece;

        return $this;
    }

    public function getImagePiece(): ?string
    {
        return $this->imagePiece;
    }

    public function setImagePiece(?string $imagePiece): static
    {
        $this->imagePiece = $imagePiece;

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

    public function getDateCreation(): ?string
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?string $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

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
