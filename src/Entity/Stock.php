<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCarte = null;

    #[ORM\Column(nullable: true)]
    private ?int $Quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function gettypeCarte(): ?string
    {
        return $this->typeCarte;
    }

    public function settypeCarte(?string $typeCarte): static
    {
        $this->typeCarte = $typeCarte;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(?int $Quantite): static
    {
        $this->Quantite = $Quantite;

        return $this;
    }
}
