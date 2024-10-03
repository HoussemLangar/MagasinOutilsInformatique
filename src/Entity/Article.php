<?php
namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Broadcast]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $niveauMin = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $niveauMax = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEntree = null;

    #[ORM\ManyToOne(targetEntity: Emplacement::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Emplacement $emplacement = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Etiquette::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $etiquettes;    

    #[ORM\ManyToMany(targetEntity: Rapport::class, mappedBy: 'articles')]
    private Collection $rapports;

    public function __construct()
    {
        $this->etiquettes = new ArrayCollection();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        if ($quantite < 0) {
            throw new \InvalidArgumentException('La quantité ne peut pas être négative.');
        }
        $this->quantite = $quantite;
        return $this;
    }

    public function getNiveauMin(): ?int
    {
        return $this->niveauMin;
    }

    public function setNiveauMin(int $niveauMin): static
    {
        $this->niveauMin = $niveauMin;
        return $this;
    }

    public function getNiveauMax(): ?int
    {
        return $this->niveauMax;
    }

    public function setNiveauMax(int $niveauMax): static
    {
        $this->niveauMax = $niveauMax;
        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->dateEntree;
    }

    public function setDateEntree(\DateTimeInterface $dateEntree): static
    {
        $this->dateEntree = $dateEntree;
        return $this;
    }

    public function getEmplacement(): ?Emplacement
    {
        return $this->emplacement;
    }

    public function setEmplacement(?Emplacement $emplacement): static
    {
        $this->emplacement = $emplacement;
        return $this;
    }

    /**
     * @return Collection<int, Etiquette>
     */
    public function getEtiquettes(): Collection
    {
        return $this->etiquettes;
    }

    public function addEtiquette(Etiquette $etiquette): static
    {
        if (!$this->etiquettes->contains($etiquette)) {
            $this->etiquettes->add($etiquette);
            $etiquette->setArticle($this);
        }

        return $this;
    }

    public function removeEtiquette(Etiquette $etiquette): static
    {
        if ($this->etiquettes->removeElement($etiquette)) {
            if ($etiquette->getArticle() === $this) {
                $etiquette->setArticle(null);
            }
        }

        return $this;
    }
}
