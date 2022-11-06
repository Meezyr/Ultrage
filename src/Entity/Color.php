<?php

namespace App\Entity;

use App\Repository\ColorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
class Color
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $paletteNumber = null;

    #[ORM\Column(length: 15)]
    private ?string $colorCode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $userAuthor = null;

    #[ORM\Column(nullable: true)]
    private array $keyword = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaletteNumber(): ?int
    {
        return $this->paletteNumber;
    }

    public function setPaletteNumber(?int $paletteNumber): self
    {
        $this->paletteNumber = $paletteNumber;

        return $this;
    }

    public function getColorCode(): ?string
    {
        return $this->colorCode;
    }

    public function setColorCode(string $colorCode): self
    {
        $this->colorCode = $colorCode;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUserAuthor(): ?user
    {
        return $this->userAuthor;
    }

    public function setUserAuthor(?user $userAuthor): self
    {
        $this->userAuthor = $userAuthor;

        return $this;
    }

    public function getKeyword(): array
    {
        return $this->keyword;
    }

    public function setKeyword(?array $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }
}
