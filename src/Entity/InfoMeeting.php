<?php

namespace App\Entity;

use App\Repository\InfoMeetingRepository;
use App\Validator\Constraints as CustomAssert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[CustomAssert\InfoMeeting]
#[ORM\Table(name: 'infomeeting')]
#[ORM\Entity(repositoryClass: InfoMeetingRepository::class)]
class InfoMeeting
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private $showOnPage;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 250, nullable: true)]
    #[Assert\DateTime]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $room = null;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    public function __toString()
    {
        return 'Infomøte';
    }

    /**
     * @return bool
     */
    public function isShowOnPage()
    {
        return $this->showOnPage;
    }

    /**
     * @param bool $showOnPage
     */
    public function setShowOnPage($showOnPage): void
    {
        $this->showOnPage = $showOnPage;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        if (mb_strlen($link) > 0 && mb_substr($link, 0, 4) !== 'http') {
            $link = "https://$link";
        }

        $this->link = $link;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
