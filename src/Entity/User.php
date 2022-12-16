<?php

namespace App\Entity;

use App\Role\Roles;
use App\Validator\Constraints as CustomAssert;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * App\Entity\User.
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *      fields={"email"},
 *      message="Denne Eposten er allerede i bruk.",
 *      groups={"create_user", "edit_user"}
 * )
 * @UniqueEntity(
 *      fields={"user_name"},
 *      message="Dette brukernavnet er allerede i bruk.",
 *      groups={"create_user", "username", "edit_user"}
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"admission", "create_user", "edit_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"admission", "create_user", "edit_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $firstName;

    /**
     * @var FieldOfStudy
     *
     * @ORM\ManyToOne(targetEntity="FieldOfStudy")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\NotBlank(groups={"admission", "edit_user", "create_user"}, message="Dette feltet kan ikke være tomt.")
     * @Assert\Valid
     */
    private $fieldOfStudy;

    /**
     * @ORM\Column(name="gender", type="boolean")
     * @Assert\NotBlank(groups={"admission", "create_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $gender;

    /**
     * @ORM\Column(type="string")
     */
    private $picture_path;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"admission", "create_user", "edit_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\NotBlank(groups={"username", "edit_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $user_name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\NotBlank(groups={"username", "edit_user"}, message="Dette feltet kan ikke være tomt.")
     */
    private $password;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(groups={"admission", "create_user", "edit_user"}, message="Dette feltet kan ikke være tomt.")
     * @Assert\Email(groups={"admission", "create_user", "edit_user"}, message="Ikke gyldig e-post.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\Email
     * @CustomAssert\UniqueCompanyEmail
     * @CustomAssert\VektorEmail
     */
    private $companyEmail;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $reservedFromPopUp;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $lastPopUpTime;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\column(type="string", nullable=true)
     */
    private $new_user_code;

    /**
     * @var AssistantHistory[]
     *
     * @ORM\OneToMany(targetEntity="AssistantHistory", mappedBy="user")
     */
    private $assistantHistories;

    /**
     * @var TeamMembership[]
     *
     * @ORM\OneToMany(targetEntity="TeamMembership", mappedBy="user")
     */
    private $teamMemberships;

    /**
     * @var ExecutiveBoardMembership[]
     *
     * @ORM\OneToMany(targetEntity="ExecutiveBoardMembership", mappedBy="user")
     */
    private $executiveBoardMemberships;

    /**
     * @ORM\OneToMany(targetEntity="CertificateRequest", mappedBy="user")
     **/
    protected $certificateRequests;

    /**
     * @ORM\OneToMany(targetEntity="Interview", mappedBy="interviewer")
     */
    private $interviews;

    /**
     * @ORM\OneToMany(targetEntity="Receipt", mappedBy="user")
     */
    private $receipts;

    public function __construct()
    {
        $this->roles = [];
        $this->certificateRequests = new ArrayCollection();
        $this->interviews = new ArrayCollection();
        $this->isActive = true;
        $this->picture_path = 'images/defaultProfile.png';
        $this->receipts = new ArrayCollection();
        $this->reservedFromPopUp = false;
        $this->lastPopUpTime = new \DateTime('2000-01-01');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDepartment(): Department
    {
        return $this->getFieldOfStudy()->getDepartment();
    }

    public function getGender(): bool
    {
        return $this->gender;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * Set lastName.
     *
     * @return User
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Set firstName.
     *
     * @return User
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Set picture_path.
     *
     * @return User
     */
    public function setPicturePath(string $picturePath)
    {
        $this->picture_path = $picturePath;

        return $this;
    }

    /**
     * Get picture_path.
     */
    public function getPicturePath(): ?string
    {
        return $this->picture_path;
    }

    /**
     * Set phone.
     *
     * @return User
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * Set user_name.
     *
     * @return User
     */
    public function setUserName(string $userName)
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get user_name.
     */
    public function getUserIdentifier(): string
    {
        return $this->user_name;
    }

    /**
     * Deprecated from Symfony 6
     * Remove when upgraded to 6.0
     * Required for now because UserInterface has this method.
     * DO NOT use this method. Use "getUserIdentifier()" instead.
     */
    public function getUsername(): string
    {
        return $this->user_name;
    }

    /**
     * Set fieldOfStudy.
     *
     * @param FieldOfStudy $fieldOfStudy
     *
     * @return User
     */
    public function setFieldOfStudy(FieldOfStudy $fieldOfStudy = null)
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    /**
     * Get fieldOfStudy.
     *
     * @return FieldOfStudy
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * Add roles.
     *
     * @return User
     */
    public function addRole(string $role)
    {
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        return $this;
    }

    /**
     * Remove roles.
     */
    public function removeRole(string $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Set new_user_code.
     *
     * @return User
     */
    public function setNewUserCode(string $newUserCode)
    {
        $this->new_user_code = $newUserCode;

        return $this;
    }

    /**
     * Get new_user_code.
     */
    public function getNewUserCode(): ?string
    {
        return $this->new_user_code;
    }

    /**
     * @return array
     */
    public function getAssistantHistories()
    {
        return $this->assistantHistories;
    }

    public function hasBeenAssistant(): bool
    {
        if ($this->assistantHistories === null) {
            return false;
        }

        return !empty($this->assistantHistories->toArray());
    }

    public function isActiveAssistant(): bool
    {
        foreach ($this->assistantHistories as $history) {
            if ($history->getSemester()->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $assistantHistories
     */
    public function setAssistantHistories($assistantHistories)
    {
        $this->assistantHistories = $assistantHistories;
    }

    public function addAssistantHistory(AssistantHistory $assistantHistory)
    {
        $this->assistantHistories[] = $assistantHistory;
    }

    /**
     * Add certificateRequests.
     *
     * @return User
     */
    public function addCertificateRequest(CertificateRequest $certificateRequests)
    {
        $this->certificateRequests[] = $certificateRequests;

        return $this;
    }

    /**
     * Remove certificateRequests.
     */
    public function removeCertificateRequest(CertificateRequest $certificateRequests)
    {
        $this->certificateRequests->removeElement($certificateRequests);
    }

    /**
     * Get certificateRequests.
     *
     * @return Collection
     */
    public function getCertificateRequests()
    {
        return $this->certificateRequests;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    // toString method used to display the user in twig files
    public function __toString()
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    /*

    You may or may not need the code below depending on the algorithm you chose to hash and salt passwords with.
    The methods below are taken from the login guide on Symfony.com, which can be found here:
    https://symfony.com/doc/current/cookbook/security/form_login_setup.html
    https://symfony.com/doc/current/cookbook/security/entity_provider.html


    */

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * @return TeamMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->teamMemberships;
    }

    /**
     * @return Interview[]
     */
    public function getInterviews()
    {
        return $this->interviews->toArray();
    }

    /**
     * @return ArrayCollection
     */
    public function getReceipts()
    {
        return $this->receipts;
    }

    /**
     * @param Receipt
     */
    public function addReceipt($receipt)
    {
        $this->receipts->add($receipt);
    }

    public function hasPendingReceipts(): bool
    {
        $numberOfPendingReceipts = $this->getNumberOfPendingReceipts();

        return $numberOfPendingReceipts !== 0;
    }

    public function getNumberOfPendingReceipts(): int
    {
        $num = 0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_PENDING) {
                ++$num;
            }
        }

        return $num;
    }

    public function getTotalPendingReceiptSum(): float
    {
        $totalSum = 0.0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_PENDING) {
                $totalSum += $receipt->getSum();
            }
        }

        return $totalSum;
    }

    public function getTotalRefundedReceiptSum(): float
    {
        $totalSum = 0.0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_REFUNDED) {
                $totalSum += $receipt->getSum();
            }
        }

        return $totalSum;
    }

    public function getTotalRejectedReceiptSum(): float
    {
        $totalSum = 0.0;
        foreach ($this->receipts as $receipt) {
            if ($receipt->getStatus() === Receipt::STATUS_REJECTED) {
                $totalSum += $receipt->getSum();
            }
        }

        return $totalSum;
    }

    public function getCompanyEmail(): ?string
    {
        return $this->companyEmail;
    }

    public function setCompanyEmail(string $companyEmail)
    {
        $this->companyEmail = $companyEmail;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getExecutiveBoardMemberships()
    {
        return $this->executiveBoardMemberships;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getActiveExecutiveBoardMemberships()
    {
        $activeExecutiveBoardMemberships = [];
        if ($this->executiveBoardMemberships !== null) {
            foreach ($this->executiveBoardMemberships as $executiveBoardMembership) {
                if ($executiveBoardMembership->isActive()) {
                    $activeExecutiveBoardMemberships[] = $executiveBoardMembership;
                }
            }
        }

        return $activeExecutiveBoardMemberships;
    }

    /**
     * @return TeamMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $activeTeamMemberships = [];
        if ($this->teamMemberships !== null) {
            foreach ($this->teamMemberships as $teamMembership) {
                if ($teamMembership->isActive()) {
                    $activeTeamMemberships[] = $teamMembership;
                }
            }
        }

        return $activeTeamMemberships;
    }

    public function getReservedFromPopUp(): bool
    {
        return $this->reservedFromPopUp;
    }

    public function setReservedFromPopUp(bool $reservedFromPopUp): void
    {
        $this->reservedFromPopUp = $reservedFromPopUp;
    }

    public function getLastPopUpTime(): \DateTime
    {
        return $this->lastPopUpTime;
    }

    /**
     * @param \DateTime $lastPopUpTime
     */
    public function setLastPopUpTime($lastPopUpTime): void
    {
        $this->lastPopUpTime = $lastPopUpTime;
    }

    /**
     * @return TeamMembershipInterface[]
     */
    public function getActiveMemberships()
    {
        return array_merge($this->getActiveTeamMemberships(), $this->getActiveExecutiveBoardMemberships());
    }

    /**
     * @param TeamMembershipInterface[] $memberships
     *
     * @return User $this
     */
    public function setMemberships($memberships)
    {
        $teamMemberships = [];
        $boardMemberships = [];
        foreach ($memberships as $membership) {
            if ($membership->getTeam()->getType() === 'team') {
                $teamMemberships[] = $membership;
            }
            if ($membership->getTeam()->getType() === 'executive_board') {
                $boardMemberships[] = $membership;
            }
        }

        $this->teamMemberships = $teamMemberships;
        $this->executiveBoardMemberships = $boardMemberships;

        return $this;
    }

    public function isAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getRole() === Roles::ADMIN) {
                return true;
            }
        }

        return false;
    }

//    public function isEqualTo(UserInterface $user): bool
//    {
//        return $this->password === $user->getPassword() && $this->user_name === $user->getUserIdentifier();
//    }
}
