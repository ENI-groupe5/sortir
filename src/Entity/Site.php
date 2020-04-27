<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=150, unique=true)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participant",mappedBy="site")
     */
    private $participants;
    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="site")
     */
    private $sorties;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants [] = $participants;
    }

    /**
     * @return
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



}
