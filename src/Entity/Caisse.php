<?php

namespace App\Entity;

use App\Repository\CaisseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaisseRepository::class)]
class Caisse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $solde = null;

    #[ORM\Column(nullable: true)]
    private ?int $depot = null;

    #[ORM\Column(nullable: true)]
    private ?int $retrait = null;

    #[ORM\OneToMany(mappedBy: 'enregistrement', targetEntity: Historique::class, )]
    private Collection $historiques;

    public function __construct()
    {
        $this->solde = 0.0;
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }
    
    public function deposer(float $montant): void
    {
        $this->solde += $montant;
    }

    public function retirer(float $montant): bool
    {
        if ($montant > $this->solde) {
            return false; // Solde insuffisant
        }

        $this->solde -= $montant;

        return true;
    }

    public function getDepot(): ?int
    {
        return $this->depot;
    }

    public function setDepot(?int $depot): static
    {
        $this->depot = $depot;

        return $this;
    }

    public function getRetrait(): ?int
    {
        return $this->retrait;
    }

    public function setRetrait(?int $retrait): static
    {
        $this->retrait = $retrait;

        return $this;
    }

    /**
     * @return Collection<int, Historique>
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): static
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques->add($historique);
            $historique->setEnregistrement($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): static
    {
        if ($this->historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getEnregistrement() === $this) {
                $historique->setEnregistrement(null);
            }
        }

        return $this;
    }
}
