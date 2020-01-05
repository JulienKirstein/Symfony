<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
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
    private $DateMiseEnLigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $Etoiles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaire", mappedBy="Com")
     */
    private $Commentaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo;


    public function getPhoto() {
      return $this->photo;
    }
    public function setPhoto($photo) {
      $this->photo = $photo;
      return $this;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $files;


    public function getFiles() {
      return $this->files;
    }
    public function setFiles($files) {
      $this->files = $files;
      return $this;
    }


    public function __construct()
    {
        $this->Commentaire = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMiseEnLigne(): ?string
    {
        return $this->DateMiseEnLigne;
    }

    public function setDateMiseEnLigne(string $DateMiseEnLigne): self
    {
        $this->DateMiseEnLigne = $DateMiseEnLigne;

        return $this;
    }

    public function getEtoiles(): ?int
    {
        return $this->Etoiles;
    }

    public function setEtoiles(int $Etoiles): self
    {
        $this->Etoiles = $Etoiles;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }


    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaire(): Collection
    {
        return $this->Commentaire;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->Commentaire->contains($commentaire)) {
            $this->Commentaire[] = $commentaire;
            $commentaire->setCom($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->Commentaire->contains($commentaire)) {
            $this->Commentaire->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getCom() === $this) {
                $commentaire->setCom(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }
}
