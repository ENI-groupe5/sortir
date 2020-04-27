<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
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
     * @ORM\Column(type="datetime")
     */
    private $datHeureDebut;
    /**
     * @ORM\Column(type="integer")
     */
    private $duree;
    /**
     * @ORM\Column(type="date")
     */
    private $dateLimiteInscription;
    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;
    /**
     * @ORM\Column(type="text")
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site",inversedBy="sorties")
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat",inversedBy="etat_sorties")
     */
    private $sortie_etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu",inversedBy="lieu_sorties")
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant",inversedBy="sortiesOrganisees")
     */
    private $organisateur;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant",mappedBy="sorties")
     */
    private $participants;
    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

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
    public function getDatHeureDebut()
    {
        return $this->datHeureDebut;
    }

    /**
     * @param mixed $datHeureDebut
     */
    public function setDatHeureDebut($datHeureDebut): void
    {
        $this->datHeureDebut = $datHeureDebut;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree): void
    {
        $this->duree = $duree;
    }

    /**
     * @return mixed
     */
    public function getDateLimiteInscription()
    {
        return $this->dateLimiteInscription;
    }

    /**
     * @param mixed $dateLimiteInscription
     */
    public function setDateLimiteInscription($dateLimiteInscription): void
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
    }

    /**
     * @return mixed
     */
    public function getNbInscriptionsMax()
    {
        return $this->nbInscriptionsMax;
    }

    /**
     * @param mixed $nbInscriptionsMax
     */
    public function setNbInscriptionsMax($nbInscriptionsMax): void
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;
    }

    /**
     * @return mixed
     */
    public function getInfosSortie()
    {
        return $this->infosSortie;
    }

    /**
     * @param mixed $infosSortie
     */
    public function setInfosSortie($infosSortie): void
    {
        $this->infosSortie = $infosSortie;
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
     * @return mixed
     */
    public function getSortieEtat()
    {
        return $this->sortie_etat;
    }

    /**
     * @param mixed $sortie_etat
     */
    public function setSortieEtat($sortie_etat): void
    {
        $this->sortie_etat = $sortie_etat;
    }

    /**
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param mixed $lieu
     */
    public function setLieu($lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param  $participants
     */
    public function setParticipants( $participants): void
    {
        $this->participants [] = $participants;
    }


}
