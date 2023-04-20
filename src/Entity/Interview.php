<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

// Static constants:
abstract class InterviewStatusType
{
    public const PENDING = 0;
    public const ACCEPTED = 1;
    public const REQUEST_NEW_TIME = 2;
    public const CANCELLED = 3;
    public const NO_CONTACT = 4;
}

#[ORM\Table(name: 'interview')]
#[ORM\Entity(repositoryClass: 'App\Repository\InterviewRepository')]
class Interview
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private $interviewed;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $scheduled = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lastScheduleChanged = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $room = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Campusnavn kan ikke være mer enn 255 tegn')]
    private ?string $campus = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: 'Linken kan ikke være mer enn 500 tegn')]
    private ?string $mapLink = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $conducted = null;

    #[ORM\ManyToOne(targetEntity: 'InterviewSchema')]
    #[ORM\JoinColumn(name: 'schema_id', referencedColumnName: 'id')]
    private ?InterviewSchema $interviewSchema = null; // Bidirectional, may turn out to be unidirectional

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'interviews')]
    #[ORM\JoinColumn(name: 'interviewer_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $interviewer; // Unidirectional, may turn out to be bidirectional

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private $coInterviewer;

    #[ORM\OneToMany(mappedBy: 'interview', targetEntity: 'InterviewAnswer', cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private Collection $interviewAnswers;

    #[ORM\OneToOne(targetEntity: 'InterviewScore', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'interview_score_id', referencedColumnName: 'id')]
    #[Assert\Valid]
    private ?InterviewScore $interviewScore = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $interviewStatus = null;

    #[ORM\ManyToOne(targetEntity: 'User', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;

    #[ORM\OneToOne(mappedBy: 'interview', targetEntity: 'Application')]
    private ?Application $application = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $responseCode = null;

    #[ORM\Column(type: 'string', length: 2000, nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $cancelMessage = null;

    #[ORM\Column(type: 'string', length: 2000)]
    #[Assert\Length(max: 2000)]
    #[Assert\NotBlank(message: 'Meldingsboksen kan ikke være tom', groups: ['newTimeRequest'])]
    private ?string $newTimeMessage = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $numAcceptInterviewRemindersSent = null;

    public function __construct()
    {
        $this->interviewAnswers = new ArrayCollection();
        $this->conducted = new \DateTime();
        $this->interviewed = false;
        $this->interviewStatus = InterviewStatusType::NO_CONTACT;
        $this->newTimeMessage = '';
        $this->numAcceptInterviewRemindersSent = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setInterviewSchema(InterviewSchema $interviewSchema = null): self
    {
        $this->interviewSchema = $interviewSchema;

        return $this;
    }

    public function getInterviewSchema(): ?InterviewSchema
    {
        return $this->interviewSchema;
    }

    /**
     * @return User
     */
    public function getCoInterviewer()
    {
        return $this->coInterviewer;
    }

    public function isCoInterviewer(User $user = null): bool
    {
        return $user && $this->getCoInterviewer() && $user->getId() === $this->getCoInterviewer()->getId();
    }

    public function setCoInterviewer(User $coInterviewer = null): self
    {
        $this->coInterviewer = $coInterviewer;

        return $this;
    }

    public function setInterviewer(User $interviewer = null): self
    {
        $this->interviewer = $interviewer;

        return $this;
    }

    /**
     * @return User
     */
    public function getInterviewer()
    {
        return $this->interviewer;
    }

    public function addInterviewAnswer(InterviewAnswer $interviewAnswers): self
    {
        $this->interviewAnswers[] = $interviewAnswers;

        return $this;
    }

    public function removeInterviewAnswer(InterviewAnswer $interviewAnswers): void
    {
        $this->interviewAnswers->removeElement($interviewAnswers);
    }

    public function getInterviewAnswers(): Collection
    {
        return $this->interviewAnswers;
    }

    public function setInterviewScore(InterviewScore $interviewScore = null): self
    {
        $this->interviewScore = $interviewScore;

        return $this;
    }

    public function getInterviewScore(): ?InterviewScore
    {
        return $this->interviewScore;
    }

    public function getScore(): ?int
    {
        if ($this->interviewScore === null) {
            return 0;
        }

        return $this->interviewScore->getSum();
    }

    /**
     * @param bool $interviewed
     */
    public function setInterviewed($interviewed): self
    {
        $this->interviewed = $interviewed;

        return $this;
    }

    /**
     * @return bool
     */
    public function getInterviewed()
    {
        return $this->interviewed;
    }

    /**
     * @return bool
     */
    public function getCancelled()
    {
        return $this->isCancelled();
    }

    /**
     * @param bool $cancelled
     */
    public function setCancelled($cancelled): void
    {
        if ($cancelled === true) {
            $this->cancel();
        } else {
            $this->acceptInterview();
        }
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom($room): void
    {
        $this->room = $room;
    }

    public function getCampus(): ?string
    {
        return $this->campus;
    }

    /**
     * @param string $campus
     */
    public function setCampus($campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getMapLink(): ?string
    {
        return $this->mapLink;
    }

    /**
     * @param string $mapLink
     */
    public function setMapLink($mapLink): void
    {
        $this->mapLink = $mapLink;
    }

    /**
     * @return bool
     */
    public function isInterviewer(User $user = null)
    {
        return $user && $user->getId() === $this->getInterviewer()->getId();
    }

    /**
     * @param \DateTime $scheduled
     */
    public function setScheduled($scheduled): self
    {
        $this->scheduled = $scheduled;
        $this->lastScheduleChanged = new \DateTime();

        return $this;
    }

    public function getScheduled(): ?\DateTime
    {
        return $this->scheduled;
    }

    /**
     * @param \DateTime $conducted
     */
    public function setConducted($conducted): self
    {
        $this->conducted = $conducted;

        return $this;
    }

    public function getConducted(): ?\DateTime
    {
        return $this->conducted;
    }

    /**
     * @return User
     */
    public function getUser()
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

    public function isDraft(): bool
    {
        return !$this->interviewed && $this->interviewScore !== null;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function getInterviewStatusAsString(): string
    {
        return match ($this->interviewStatus) {
            InterviewStatusType::NO_CONTACT => 'Ikke satt opp',
            InterviewStatusType::PENDING => 'Ingen svar',
            InterviewStatusType::ACCEPTED => 'Akseptert',
            InterviewStatusType::REQUEST_NEW_TIME => 'Ny tid ønskes',
            InterviewStatusType::CANCELLED => 'Kansellert',
            default => 'Ingen svar',
        };
    }

    public function getInterviewStatusAsColor(): string
    {
        return match ($this->interviewStatus) {
            InterviewStatusType::NO_CONTACT => '#9999ff',
            InterviewStatusType::PENDING => '#0d97c4',
            InterviewStatusType::ACCEPTED => '#32CD32',
            InterviewStatusType::REQUEST_NEW_TIME => '#F08A24',
            InterviewStatusType::CANCELLED => '#f40f0f',
            default => '#000000',
        };
    }

    public function isPending(): bool
    {
        return $this->interviewStatus === InterviewStatusType::PENDING;
    }

    public function setInterviewStatus(int $interviewStatus): void
    {
        $this->interviewStatus = $interviewStatus;
    }

    public function acceptInterview(): void
    {
        $this->setInterviewStatus(InterviewStatusType::ACCEPTED);
    }

    public function requestNewTime(): void
    {
        $this->setInterviewStatus(InterviewStatusType::REQUEST_NEW_TIME);
    }

    public function cancel(): void
    {
        $this->setInterviewStatus(InterviewStatusType::CANCELLED);
    }

    public function resetStatus(): void
    {
        $this->setInterviewStatus(InterviewStatusType::PENDING);
    }

    public function isCancelled(): bool
    {
        return $this->interviewStatus === InterviewStatusType::CANCELLED;
    }

    public function getResponseCode(): ?string
    {
        return $this->responseCode;
    }

    public function setResponseCode(string $responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return string
     */
    public function generateAndSetResponseCode()
    {
        $newResponseCode = bin2hex(openssl_random_pseudo_bytes(12));
        $this->responseCode = $newResponseCode;

        return $newResponseCode;
    }

    public function getCancelMessage(): string
    {
        if ($this->cancelMessage !== null) {
            return $this->cancelMessage;
        }

        return '';
    }

    public function setCancelMessage(string $cancelMessage = null): void
    {
        $this->cancelMessage = $cancelMessage;
    }

    public function setStatus(int $newStatus): void
    {
        if ($newStatus >= 0 && $newStatus <= 4) {
            $this->interviewStatus = $newStatus;
        } else {
            throw new \InvalidArgumentException('Invalid status');
        }
    }

    public function getNewTimeMessage(): string
    {
        return $this->newTimeMessage;
    }

    /**
     * @param string $newTimeMessage
     */
    public function setNewTimeMessage($newTimeMessage): void
    {
        $this->newTimeMessage = $newTimeMessage;
    }

    public function getInterviewStatus(): int
    {
        return $this->interviewStatus;
    }

    public function getLastScheduleChanged(): ?\DateTime
    {
        return $this->lastScheduleChanged;
    }

    public function getNumAcceptInterviewRemindersSent(): ?int
    {
        return $this->numAcceptInterviewRemindersSent;
    }

    public function setNumAcceptInterviewRemindersSent(int $numAcceptInterviewRemindersSent): Interview
    {
        $this->numAcceptInterviewRemindersSent = $numAcceptInterviewRemindersSent;

        return $this;
    }

    /**
     * Increments number of accept-interview reminders sent.
     */
    public function incrementNumAcceptInterviewRemindersSent(): void
    {
        ++$this->numAcceptInterviewRemindersSent;
    }
}
