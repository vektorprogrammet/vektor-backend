<?php

namespace App\Command;

use App\Service\InterviewManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAcceptInterviewReminderCommand extends Command
{
    // TODO: Use dependency-injection for dependencies
    private InterviewManager $interviewManager;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:send_accept_interview_reminder')
            ->setDescription('Send an email reminder to all users with unaccepted interviews');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->interviewManager = $this->getContainer()->get(InterviewManager::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->interviewManager->sendAcceptInterviewReminders();

        return Command::SUCCESS;
    }
}
