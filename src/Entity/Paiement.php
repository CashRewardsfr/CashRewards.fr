<?php

namespace App\Entity;

use App\Entity\Traits\Timestamp;
use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Paiement
{
    use Timestamp;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'float')]
    private $montant;

    #[ORM\Column(type: 'boolean')]
    private $statut;

    #[ORM\Column(type: 'string', length: 255)]
    private $virementEmail;

    #[ORM\Column(type: 'string', length: 255)]
    private $virementType;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $deleted;

    #[ORM\Column(type: 'integer')]
    private $pointReduction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getVirementEmail(): ?string
    {
        return $this->virementEmail;
    }

    public function setVirementEmail(string $virementEmail): self
    {
        $this->virementEmail = $virementEmail;

        return $this;
    }

    public function getVirementType(): ?string
    {
        return $this->virementType;
    }

    public function setVirementType(string $virementType): self
    {
        $this->virementType = $virementType;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getPointReduction(): ?int
    {
        return $this->pointReduction;
    }

    public function setPointReduction(int $pointReduction): self
    {
        $this->pointReduction = $pointReduction;

        return $this;
    }
}
