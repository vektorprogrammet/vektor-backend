<?php

namespace App\Core\Domain\Entity;

use App\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'user')]
#[ORM\Entity]
#[UniqueEntity(fields: ['email'], message: 'Denne Eposten er allerede i bruk.', groups: ['create_user', 'edit_user'])]
#[UniqueEntity(fields: ['user_name'], message: 'Dette brukernavnet er allerede i bruk.', groups: ['create_user', 'username', 'edit_user'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'create_user', 'edit_user'])]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'create_user', 'edit_user'])]
    private ?string $firstName = null;

    #[ORM\ManyToOne(targetEntity: FieldOfStudy::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'edit_user', 'create_user'])]
    #[Assert\Valid]
    private ?FieldOfStudy $fieldOfStudy = null;

    #[ORM\Column(name: 'gender', type: Types::BOOLEAN)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'create_user'])]
    private $gender;

    #[ORM\Column(type: Types::STRING)]
    private ?string $picture_path = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'create_user', 'edit_user'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, length: 45, nullable: true)]
    private ?string $accountNumber = null;

    #[ORM\Column(type: Types::STRING, unique: true, nullable: true)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['username', 'edit_user'])]
    private ?string $user_name = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['username', 'edit_user'])]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'create_user', 'edit_user'])]
    #[Assert\Email(message: 'Ikke gyldig e-post.', groups: ['admission', 'create_user', 'edit_user'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, unique: true, nullable: true)]
    #[Assert\Email]
    #[CustomAssert\UniqueCompanyEmail]
    #[CustomAssert\VektorEmail]
    private ?string $companyEmail = null;

    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN)]
    private bool $isActive;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $new_user_code = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AssistantHistory::class)]
    private Collection $assistantHistories;

    /**
     * @var TeamMembership[]
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TeamMembership::class, cascade: ['remove'])]
    private $teamMemberships;

    /**
     * @var ExecutiveBoardMembership[]
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ExecutiveBoardMembership::class, cascade: ['remove'])]
    private $executiveBoardMemberships;

    public function __construct()
    {
        $this->roles = [];
        $this->isActive = true;
        $this->picture_path = 'images/defaultProfile.png';
        $this->assistantHistories = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getEmail(): ?string
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function setActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setGender($gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function setPicturePath(string $picturePath): self
    {
        $this->picture_path = $picturePath;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picture_path;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber($accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    public function setUserName(string $userName): self
    {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get user_name.
     */
    public function getUserIdentifier(): ?string
    {
        return $this->user_name;
    }

    /**
     * Deprecated from Symfony 6
     * Remove when upgraded to 6.0
     * Required for now because UserInterface has this method.
     * DO NOT use this method. Use "getUserIdentifier()" instead.
     */
    public function getUsername(): ?string
    {
        return $this->user_name;
    }

    public function setFieldOfStudy(FieldOfStudy $fieldOfStudy = null): self
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    public function getFieldOfStudy(): ?FieldOfStudy
    {
        return $this->fieldOfStudy;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);

        return $this;
    }

    public function removeRole(string $roles): void
    {
        // remove role from array
        $this->roles = array_diff($this->roles, [$roles]);
    }

    public function setNewUserCode(string $newUserCode): self
    {
        $this->new_user_code = $newUserCode;

        return $this;
    }

    public function getNewUserCode(): ?string
    {
        return $this->new_user_code;
    }

    public function getAssistantHistories(): Collection
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

    public function setAssistantHistories(ArrayCollection $assistantHistories): void
    {
        $this->assistantHistories = $assistantHistories;
    }

    public function addAssistantHistory(AssistantHistory $assistantHistory): void
    {
        $this->assistantHistories->add($assistantHistory);
    }

    // Used for unit testing
    public function fromArray($data = []): void
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
     * @param TeamMembershipInterface[] $memberships
     */
    public function setMemberships($memberships): self
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
}
