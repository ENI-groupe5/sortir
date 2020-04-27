<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LieuRepository")
 */
class Lieu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $nom;
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $rue;
    /**
     * @ORM\Column(type="float")
     */
    private $latitude;
    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie",mappedBy="lieu")
     */
    private $lieu_sorties;
    public function __construct()
    {
        $this->lieu_sorties = new ArrayCollection();
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville",inversedBy="ville_lieux")
     */
    private $lieu_ville;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getRue()
    {
        return $this->rue;
    }

    /**
     * @param mixed $rue
     */
    public function setRue($rue): void
    {
        $this->rue = $rue;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return
     */
    public function getLieuSorties()
    {
        return $this->lieu_sorties;
    }

    /**
     * @param  $lieu_sorties
     */
    public function setLieuSorties( $lieu_sorties): void
    {
        $this->lieu_sorties [] = $lieu_sorties;
    }

    /**
     * @return mixed
     */
    public function getLieuVille()
    {
        return $this->lieu_ville;
    }

    /**
     * @param mixed $lieu_ville
     */
    public function setLieuVille($lieu_ville): void
    {
        $this->lieu_ville = $lieu_ville;
    }



}
