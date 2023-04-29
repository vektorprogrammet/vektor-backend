<?php

namespace App\Command;

use App\Service\AdmissionNotifier;
use App\Service\TeamMembershipService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateTeamMembershipCommand extends Command
{
    public function __construct(
        private readonly TeamMembershipService $teamMembershipService,
        private readonly AdmissionNotifier $notifier
    ) {
        parent::__construct();
    }

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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifier->updateTeamMemberships();

        return Command::SUCCESS;
    }
}
