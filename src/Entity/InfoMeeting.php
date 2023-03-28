<?php

namespace App\Entity;

use App\Validator\Constraints as CustomAssert;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="infomeeting")
 * @ORM\Entity(repositoryClass="App\Repository\InfoMeetingRepository")
 * @CustomAssert\InfoMeeting()
 */
class InfoMeeting
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $showOnPage;

    /**
     * @ORM\Column(type="datetime", length=250, nullable=true)
     * @Assert\Type("\DateTimeInterface")
     */
    private ?DateTime $date = null;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private string $room;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private string $link;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): InfoMeeting
    {
        $this->date = $date;
        return $this;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): InfoMeeting
    {
        $this->room = $room;
        return $this;
    }

    public function __toString()
    {
        return 'Infomøte';
    }

    public function isShowOnPage(): bool
    {
        return $this->showOnPage;
    }

    public function setShowOnPage(bool $showOnPage): InfoMeeting
    {
        $this->showOnPage = $showOnPage;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): InfoMeeting
    {
        if (mb_strlen($link) > 0 && mb_substr($link, 0, 4) !== 'http') {
            $link = "https://$link";
        }

        $this->link = $link;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): InfoMeeting
    {
        $this->description = $description;
        return $this;
    }
}
