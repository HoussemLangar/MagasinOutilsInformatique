<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $dateSortie = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'operations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\ManyToMany(targetEntity: Rapport::class, mappedBy: 'operations')]
    private Collection $rapports;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $type = null;
    
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $numOperation = null;

    public function getNumOperation(): ?int
    {
        return $this->numOperation;
    }

    public function setNumOperation(?int $numOperation): self
    {
        $this->numOperation = $numOperation;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;
        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function getRapports(): Collection
    {
        return $this->rapports;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    } 

    public function addRapport(Rapport $rapport): self
    {
        if (!$this->rapports->contains($rapport)) {
            $this->rapports->add($rapport);
            $rapport->addOperation($this);
        }

        return $this;
    }

    public function removeRapport(Rapport $rapport): self
    {
        if ($this->rapports->removeElement($rapport)) {
            $rapport->removeOperation($this);
        }

        return $this;
    }
}
