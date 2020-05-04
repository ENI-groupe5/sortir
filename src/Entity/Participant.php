<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 * @Vich\Uploadable
 */
class Participant implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=50)
     */
    private $nom;
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;
    /**
     * @ORM\Column(type="string", nullable=true, length=20)
     */
    private $telephone;
    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $email;
    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site",inversedBy="participants")
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie",mappedBy="organisateur")
     */
    private $sortiesOrganisees;

    /**
     * @ORM\Column(type="string", name="avatar", nullable=true)
     * @var string|null
     */
    private $avatar;

    /**
     * @Vich\UploadableField(mapping="avatar_participant", fileNameProperty="avatar")
     * @var File|null
     */
    private $avatarFile;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTimeInterface|null
     */
    private $updatedAt;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie",inversedBy="participants")
     */
    private $sorties;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * @param mixed $actif
     */
    public function setActif($actif): void
    {
        $this->actif = $actif;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return ArrayCollection
     */
    public function getSortiesOrganisees()
    {
        return $this->sortiesOrganisees;
    }

    /**
     * @param  $sortiesOrganisees
     */
    public function setSortiesOrganisees( $sortiesOrganisees): void
    {
        $this->sortiesOrganisees [] = $sortiesOrganisees;
    }

    /**
     * @return ArrayCollection
     */
    public function getSorties()
    {
        return $this->sorties;
    }

    /**
     * @param  $sorties
     */
    public function setSorties( $sorties): void
    {
        $this->sorties [] = $sorties;
    }

    /**
     * @return string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return File|null
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @param File|null $avatarFile
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;
        if ($this->avatarFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->prenom,
            $this->nom,
            $this->email,
            $this->telephone,
            $this->password,
            $this->site,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->prenom,
            $this->nom,
            $this->email,
            $this->telephone,
            $this->password,
            $this->site,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
