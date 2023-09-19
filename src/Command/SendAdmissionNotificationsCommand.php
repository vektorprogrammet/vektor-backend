<?php

namespace App\Command;

use App\Service\AdmissionNotifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendAdmissionNotificationsCommand extends Command
{
    // TODO: Use dependency-injection for dependencies
    private AdmissionNotifier $notifier;

    protected function configure()
    {
        $this
            ->setName('app:admission:send_notifications')
            ->setDescription('Sends notifications about active admission period to subscribers');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notifier = $this->getContainer()->get(AdmissionNotifier::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->notifier->sendAdmissionNotifications();

        return Command::SUCCESS;
    }
}
