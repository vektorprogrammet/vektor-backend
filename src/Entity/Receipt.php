<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'receipt')]
#[ORM\Entity(repositoryClass: 'App\Repository\ReceiptRepository')]
class Receipt
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'receipts')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $submitDate = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?\DateTime $receiptDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $refundDate = null;

    #[ORM\Column(name: 'picture_path', type: 'string', nullable: true)]
    private ?string $picturePath = null;

    #[ORM\Column(type: 'string', length: 5000)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 5000, maxMessage: 'Maks 5000 tegn')]
    private ?string $description = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\GreaterThan(0, message: 'Ugyldig sum')]
    private ?float $sum = null;

    #[ORM\Column(type: 'string')]
    private ?string $status = null;

    #[ORM\Column(name: 'visual_id', type: 'string', nullable: true)]
    private ?string $visualId = null;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->submitDate = new \DateTime();
        $this->receiptDate = new \DateTime();
        $currentTimeInMilliseconds = round(microtime(true) * 1000);
        $this->visualId = dechex($currentTimeInMilliseconds);
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getSubmitDate(): \DateTime
    {
        return $this->submitDate;
    }

    /**
     * @param \DateTime $submitDate
     */
    public function setSubmitDate($submitDate): void
    {
        $this->submitDate = $submitDate;
    }

    public function getReceiptDate(): \DateTime
    {
        return $this->receiptDate;
    }

    /**
     * @param \DateTime $receiptDate
     */
    public function setReceiptDate($receiptDate): void
    {
        $this->receiptDate = $receiptDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    /**
     * @param string $picturePath
     */
    public function setPicturePath($picturePath): void
    {
        $this->picturePath = $picturePath;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): void
    {
        $this->sum = $sum;
    }

    public function getVisualId(): string
    {
        return $this->visualId;
    }

    public function setVisualId(string $visualId): void
    {
        $this->visualId = $visualId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->visualId;
    }

    public function getRefundDate(): ?\DateTime
    {
        return $this->refundDate;
    }

    public function setRefundDate(\DateTime $refundDate): void
    {
        $this->refundDate = $refundDate;
    }
}
