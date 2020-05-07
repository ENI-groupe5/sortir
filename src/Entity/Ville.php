<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields={"nom","codePostal"})
 * @ORM\Entity(repositoryClass="App\Repository\VilleRepository")
 */
class Ville
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min="1",max="255",minMessage="nom trop court! min 1 caractères",maxMessage="nom trop long!! max 255 caractères")
     * @ORM\Column(type="string",length=255)
     */
    private $nom;
    /**
     * @Assert\NotBlank(message="veuillez inserer un code postal")
     * @Assert\Length(min="5",max="5",minMessage="le code postal doit contenir 5 caracteres",maxMessage="le code postal doit contenir 5 caracteres")
     * @Assert\Regex(pattern="%^[0-9]{5,5}$%",message="Le code postal doit contenir 5 chiffres")
     * @ORM\Column(type="string",length=5)
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lieu",mappedBy="lieu_ville")
     */
    private $ville_lieux;
    public function __construct()
    {
        $this->ville_lieux = new ArrayCollection();
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
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @param mixed $codePostal
     */
    public function setCodePostal($codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    /**
     * @return
     */
    public function getVilleLieux()
    {
        return $this->ville_lieux;
    }

    /**
     * @param  $ville_lieux
     */
    public function setVilleLieux( $ville_lieux): void
    {
        $this->ville_lieux [] = $ville_lieux;
    }

}
