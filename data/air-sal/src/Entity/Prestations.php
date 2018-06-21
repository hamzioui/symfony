<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrestationsRepository")
 */
class Prestations
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price_surface;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price_user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price_fixed;

    /**
     * @MaxDepth(1)
     * @ORM\ManyToMany(targetEntity="App\Entity\Reservation", mappedBy="Prestations")
     */
    private $reservations;

    /**
     * @MaxDepth(1)
     * @ORM\ManyToMany(targetEntity="App\Entity\Salle", mappedBy="Prestations")
     */
    private $salles;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->salles = new ArrayCollection();
    }

    public function __toString() {
        if( $name = $this->getName() ) {
            return $name;
        }
    
        // if no translation has been added, return empty string instead.
        return '';
    }

    

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPriceSurface(): ?int
    {
        return $this->price_surface;
    }

    public function setPriceSurface(?int $price_surface): self
    {
        $this->price_surface = $price_surface;

        return $this;
    }

    public function getPriceUser(): ?int
    {
        return $this->price_user;
    }

    public function setPriceUser(?int $price_user): self
    {
        $this->price_user = $price_user;

        return $this;
    }

    public function getPriceFixed(): ?int
    {
        return $this->price_fixed;
    }

    public function setPriceFixed(?int $price_fixed): self
    {
        $this->price_fixed = $price_fixed;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addPrestation($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            $reservation->removePrestation($this);
        }

        return $this;
    }

    /**
     * @return Collection|Salle[]
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): self
    {
        if (!$this->salles->contains($salle)) {
            $this->salles[] = $salle;
            $salle->addPrestation($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): self
    {
        if ($this->salles->contains($salle)) {
            $this->salles->removeElement($salle);
            $salle->removePrestation($this);
        }

        return $this;
    }

}
