<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=RecetteRepository::class)
 */
class Recette
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:recette"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:recette"})
     */
    private $tempsPreparation;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:recette"})
     */
    private $cout;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:recette"})
     */
    private $nbPersonne;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:recette"})
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:recette"})
     */
    private $nom;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read:recette"})
     */
    private $public;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recettes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read:recette"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, mappedBy="recettes")
     * @Groups({"read:recette"})
     */
    private $categories;

    public function __construct()
    {
        $this->dateCreation = new \DateTime('now');
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTempsPreparation(): ?int
    {
        return $this->tempsPreparation;
    }

    public function setTempsPreparation(int $tempsPreparation): self
    {
        $this->tempsPreparation = $tempsPreparation;

        return $this;
    }

    public function getCout(): ?int
    {
        return $this->cout;
    }

    public function setCout(int $cout): self
    {
        $this->cout = $cout;

        return $this;
    }

    public function getNbPersonne(): ?int
    {
        return $this->nbPersonne;
    }

    public function setNbPersonne(int $nbPersonne): self
    {
        $this->nbPersonne = $nbPersonne;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
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

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addRecette($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeRecette($this);
        }

        return $this;
    }
}
