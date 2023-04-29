<?php

namespace App\Command;

use App\Entity\User;
use App\Service\RoleManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUserRolesCommand extends Command
{
    public function __construct(private readonly RoleManager $roleManager)
    {
        parent::__construct();
    }

    /**
     * @var ObjectManager
     */
    private $entityManager;
    private int $rolesUpdatedCount;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:update:roles')
            ->setDescription('Updates all user roles')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command will update all user roles:
  <info>php %command.full_name%</info>
Assistant users that are in teams will be promoted to Team members.
Users NOT in team will be demoted to Assistants.
HELP
            );
    }

    /**
     * This method is executed before the execute-method. It's main purpose
     * is to initialize the variables used in the rest of the command methods.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $this->rolesUpdatedCount = 0;
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = microtime(true);

        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $roleUpdated = $this->roleManager->updateUserRole($user);
            if ($roleUpdated) {
                ++$this->rolesUpdatedCount;
            }
        }

        $this->entityManager->flush();

        $finishTime = microtime(true);
        $elapsedTime = ($finishTime - $startTime) * 1000;

        $output->writeln(sprintf('%d roles updated in %d ms', $this->rolesUpdatedCount, $elapsedTime));

        return Command::SUCCESS;
    }
}
