<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'sponsor')]
#[ORM\Entity]
class Sponsor
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'Feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Feltet kan ikke være tomt.')]
    protected ?string $url = null;

    /**
     * Available sizes: "small", "medium" and "large".
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Feltet kan ikke være tomt.')]
    protected ?string $size = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $logoImagePath = null;

    /**
     * Default image size to medium.
     */
    public function __construct()
    {
        $this->size = 'medium';
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setLogoImagePath(?string $logoImagePath): self
    {
        $this->logoImagePath = $logoImagePath;

        return $this;
    }

    public function getLogoImagePath(): ?string
    {
        return $this->logoImagePath;
    }
}
