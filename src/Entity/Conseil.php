<?php

namespace App\Entity;

use App\Repository\ConseilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConseilRepository::class)]
class Conseil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Doit être renseigné")]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Doit être renseigné")]
    private ?string $title = null;

    /**
     * @var Collection<int, Mois>
     */
    #[ORM\ManyToMany(targetEntity: Mois::class, mappedBy: 'conseils')]
    #[Assert\Count(min:1, max:12, minMessage:'Doit contenir ')]
    private Collection $moisList;

    public function __construct()
    {
        $this->moisList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Mois>
     */
    public function getMoisList(): Collection
    {
        return $this->moisList;
    }

    public function addMoisList(Mois $mois): static
    {
        if (!$this->moisList->contains($mois)) {
            $this->moisList->add($mois);
            $mois->addConseil($this);
        }

        return $this;
    }

    public function removeMoisList(Mois $mois): static
    {
        if ($this->moisList->removeElement($mois)) {
            $mois->removeConseil($this);
        }

        return $this;
    }

    public function setMoisList(Collection $moisList): static
    {
        $this->moisList = $moisList;
        return $this;
    }
}
