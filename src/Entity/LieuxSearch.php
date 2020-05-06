<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


class LieuxSearch
{


    private $libelle;



    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
}
