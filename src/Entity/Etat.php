<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtatRepository")
 */
class Etat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",unique=true)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie",mappedBy="sortie_etat")
     */
    private $etat_sorties;
    public function __construct()
    {
        $this->etat_sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * @return
     */
    public function getEtatSorties()
    {
        return $this->etat_sorties;
    }

    /**
     * @param  $etat_sorties
     */
    public function setEtatSorties( $etat_sorties): void
    {
        $this->etat_sorties [] = $etat_sorties;
    }


}
