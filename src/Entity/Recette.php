<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 *
 * @OA\Schema()
 * @OA\Parameter(
 *          name="orderBy[nom]",
 *          in="query",
 *          description="Order by a nom",
 *          @OA\Schema(type="array", @OA\Items(type="string", enum={"asc","desc"}))
 * ),
 * @OA\Parameter(
 *          name="orderBy[cout]",
 *          in="query",
 *          description="Order by a cout",
 *
 *          @OA\Schema(type="array", @OA\Items(type="string",maxProperties=1, enum={"asc","desc"}))
 * ),
 * @OA\Parameter(
 *          name="orderBy[nbPersonne]",
 *          in="query",
 *          description="Order by a nbPersonne",
 *          @OA\Schema(type="array", @OA\Items(type="string", enum={"asc","desc"}))
 * ),
 * @OA\Parameter(
 *          name="orderBy[dateCreation]",
 *          in="query",
 *          description="Order by a dateCreation",
 *          @OA\Schema(type="array", @OA\Items(type="string", enum={"asc","desc"}))
 * ),
 * @OA\Parameter(
 *          name="orderBy[tempsPreparation]",
 *          in="query",
 *          description="Order by a tempsPreparation",
 *          @OA\Schema(type="array", @OA\Items(type="string", enum={"asc","desc"}))
 * ),
 * @OA\Parameter(
 *          name="limit",
 *          in="query",
 *          description="Limit the number of resource",
 *          @OA\Schema(type="integer")
 * ),
 * @OA\Parameter(
 *          name="query",
 *          in="query",
 *          description="Search recettes with a query string",
 *          @OA\Schema(type="string")
 * ),
 * @ORM\Entity(repositoryClass=RecetteRepository::class)
 *
 * @UniqueEntity("nom")
 */


class Recette
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:recette", "post:recette", "read:categorie"})
     *
     * @OA\Property(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:recette", "post:recette","read:categorie"})
     *
     * @OA\Property(type="integer", default="55")
     */
    private $tempsPreparation;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:recette", "post:recette","read:categorie"})
     *
     * @OA\Property(type="integer")
     */
    private $cout;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:recette", "post:recette","read:categorie"})
     * @Assert\Range(min="10")
     * @OA\Property(type="integer")
     */
    private $nbPersonne;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:recette","read:categorie"})
     *
     * @OA\Property(type="string", format="date-time")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:recette", "post:recette","read:categorie"})
     * @Assert\NotBlank(message="Ce champ ne peut pas être vide.")
     * @OA\Property(type="string")
     */
    private $nom;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read:recette", "post:recette","read:categorie"})
     * @OA\Property(type="boolean")
     */
    private $public;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recettes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read:recette", "post:recette","read:categorie"})
     *
     * @OA\Property (type="object", ref="#/components/schemas/UserGetCollectionRecette")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, mappedBy="recettes")
     * @Groups({"read:recette", "post:recette"})
     *
     * @OA\Property(type="array", @OA\Items(ref="#/components/schemas/Categorie"))
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
