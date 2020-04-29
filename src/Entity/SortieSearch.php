<?php

namespace App\Entity;




use Doctrine\Common\Collections\ArrayCollection;

class SortieSearch
{


    /**
     *
     *
     */
    private $libelle;


    /**
     *
     */
    private $dateDebut;

    private $dateFin;

    private $sites;

    private $organisateur;

    private $inscrits;

    private $inscrit;

    private $past;

    private $noinscrit;






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
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param mixed $dateDebut
     */
    public function setDateDebut($dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateFin
     */
    public function setDateFin($dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return mixed
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @param mixed $sites
     */
    public function setSites($sites): void
    {
        $this->sites = $sites;
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
     * @return mixed
     */
    public function getInscrits()
    {
        return $this->inscrits;
    }

    /**
     * @param mixed $inscrits
     */
    public function setInscrits($inscrits): void
    {
        $this->inscrits = $inscrits;
    }

    /**
     * @return mixed
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * @param mixed $inscrit
     */
    public function setInscrit($inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return mixed
     */
    public function getPast()
    {
        return $this->past;
    }

    /**
     * @param mixed $past
     */
    public function setPast($past): void
    {
        $this->past = $past;
    }

    /**
     * @return mixed
     */
    public function getNoinscrit()
    {
        return $this->noinscrit;
    }

    /**
     * @param mixed $noinscrit
     */
    public function setNoinscrit($noinscrit): void
    {
        $this->noinscrit = $noinscrit;
    }





}
