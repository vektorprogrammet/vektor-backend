<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="sponsor")
 */
class Sponsor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\NotBlank(message="Feltet kan ikke være tomt.")
     */
    protected ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Feltet kan ikke være tomt.")
     */
    protected ?string $url;

    /**
     * Available sizes: "small", "medium" and "large".
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Feltet kan ikke være tomt.")
     */
    protected ?string $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $logoImagePath = null;

    /**
     * Sponsor constructor. Default image size to medium.
     */
    public function __construct()
    {
        $this->size = 'medium';
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): Sponsor
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set Sponsor Name.
     */
    public function setName(string $name): Sponsor
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name of Sponsor.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set Sponsor website URL.
     */
    public function setUrl(?string $url): Sponsor
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get Sponsor URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set logoImagePath.
     */
    public function setLogoImagePath(?string $logoImagePath): Sponsor
    {
        $this->logoImagePath = $logoImagePath;

        return $this;
    }

    /**
     * Get logoImagePath.
     */
    public function getLogoImagePath(): ?string
    {
        return $this->logoImagePath;
    }
}
