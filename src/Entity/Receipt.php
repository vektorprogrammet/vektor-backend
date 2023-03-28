<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptRepository")
 * @ORM\Table(name="receipt")
 */
class Receipt
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_REJECTED = 'rejected';
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="receipts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $submitDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private DateTime $receiptDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $refundDate;

    /**
     * @ORM\Column(name="picture_path", type="string", nullable=true)
     */
    private ?string $picturePath = null;

    /**
     * @ORM\Column(type="string", length=5000)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Length(max="5000", maxMessage="Maks 5000 tegn")
     */
    private string $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\GreaterThan(0, message="Ugyldig sum")
     */
    private float $sum;

    /**
     * @ORM\Column(type="string")
     */
    private string $status;

    /**
     * @ORM\Column(name="visual_id", type="string", nullable=true)
     */
    private string $visualId;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->submitDate = new DateTime();
        $this->receiptDate = new DateTime();
        $currentTimeInMilliseconds = round(microtime(true) * 1000);
        $this->visualId = dechex($currentTimeInMilliseconds);
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getSubmitDate(): DateTime
    {
        return $this->submitDate;
    }

    public function setSubmitDate(DateTime $submitDate): Receipt
    {
        $this->submitDate = $submitDate;
        return $this;
    }

    public function getReceiptDate(): DateTime
    {
        return $this->receiptDate;
    }

    public function setReceiptDate(DateTime $receiptDate): Receipt
    {
        $this->receiptDate = $receiptDate;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPicturePath(): string
    {
        return $this->picturePath;
    }

    public function setPicturePath(?string $picturePath): Receipt
    {
        $this->picturePath = $picturePath;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Receipt
    {
        $this->description = $description;
        return $this;
    }

    public function getSum(): float
    {
        return $this->sum;
    }

    public function setSum(float $sum): Receipt
    {
        $this->sum = $sum;
        return $this;
    }

    public function getVisualId(): string
    {
        return $this->visualId;
    }

    public function setVisualId(string $visualId): Receipt
    {
        $this->visualId = $visualId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Receipt
    {
        $this->status = $status;
        return $this;
    }

    public function __toString()
    {
        return $this->visualId;
    }

    public function getRefundDate(): DateTime
    {
        return $this->refundDate;
    }

    public function setRefundDate(DateTime $refundDate): Receipt
    {
        $this->refundDate = $refundDate;
        return $this;
    }
}
