<?php

namespace App\Command;

use App\Service\TeamMembershipService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTeamMembershipCommand extends Command
{
    /**
     * @var TeamMembershipService
     */
    private $teamMembershipService;
    public function __construct(TeamMembershipService $teamMembershipService)
    {
        $this->teamMembershipService = $teamMembershipService;
        parent::__construct();
    }

    /**
     * @var TeamMembershipService
     */
    private $notifier;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:update:team_membership')
            ->setDescription('Looks for expired team memberships');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notifier = $this->teamMembershipService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->notifier->updateTeamMemberships();
    }
}
