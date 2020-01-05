<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaireRepository")
 */
class Commentaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Commentaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application", inversedBy="Commentaire")
     */
    private $Com;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(string $Commentaire): self
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }

    public function getCom(): ?Application
    {
        return $this->Com;
    }

    public function setCom(?Application $Com): self
    {
        $this->Com = $Com;

        return $this;
    }
}
