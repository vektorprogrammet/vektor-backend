<?php

namespace App\Core\Domain\Interfaces;

interface ITeam
{
    public function getName(): ?string;

    public function getEmail(): ?string;

    public function setEmail(string $email);

    public function getType(): string;

    public function getDescription(): ?string;

    public function setDescription(string $description);

    public function getShortDescription(): ?string;

    public function setShortDescription(string $shortDescription);

    public function getAcceptApplication(): bool;

    /**
     * @return TeamMembershipInterface
     */
    public function getTeamMemberships();

    /**
     * @return TeamMembershipInterface
     */
    public function getActiveTeamMemberships();

    /**
     * @return User
     */
    public function getActiveUsers();
}
