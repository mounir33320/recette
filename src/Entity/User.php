<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @OA\Schema()
 *
 * @OA\Schema(
 *     schema="UserGetCollectionRecette",
 *     @OA\Property(property="firstname", type="string"),
 *     @OA\Property(property="lastname", type="string")
 * )
 *
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:recette"})
     *
     * @OA\Property(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @OA\Property(type="string", default="john@doe.fr")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:recette"})
     *
     * @OA\Property(type="string", default="John")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:recette"})
     *
     * @OA\Property(type="string", default="Doe")
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity=Recette::class, mappedBy="user", orphanRemoval=true)
     *
     * @OA\Property(type="array", @OA\Items(ref="#/components/schemas/Recette"))
     */
    private $recettes;

    /**
     * @ORM\Column(type="boolean", options = {"default" = true})
     */
    private $actif=true;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection|Recette[]
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
            $recette->setUser($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getUser() === $this) {
                $recette->setUser(null);
            }
        }

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        if (in_array(["ROLE_ADMIN"],$this->getRoles())) {
            $this->actif = true;
            return $this;
        }

        $this->actif = $actif;

        return $this;
    }
}
