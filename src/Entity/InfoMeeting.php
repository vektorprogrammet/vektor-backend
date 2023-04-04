<?php

namespace App\Entity;

use App\Validator\Constraints as CustomAssert;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @CustomAssert\InfoMeeting()
 */
#[ORM\Table(name: 'infomeeting')]
#[ORM\Entity(repositoryClass: 'App\Repository\InfoMeetingRepository')]
class InfoMeeting
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private $showOnPage;

    #[ORM\Column(type: 'datetime', length: 250, nullable: true)]
    #[Assert\DateTime]
    private $date;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $room = null;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
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
    public function setShowOnPage($showOnPage)
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
