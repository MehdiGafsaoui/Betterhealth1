<?php

namespace App\Command;

use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\DemandeDonSang;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:send-donation-reminders')]
class SendDonationRemindersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;

    public function __construct(EntityManagerInterface $entityManager, NotificationService $notificationService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $threeMonthsAgo = (new \DateTime())->modify('-3 months');
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $lastDonation = $this->entityManager->getRepository(DemandeDonSang::class)
                ->findOneBy(['user' => $user], ['createdAt' => 'DESC']);

            if ($lastDonation && $lastDonation->getCreatedAt() <= $threeMonthsAgo) {
                $this->notificationService->createNotification($user, "Vous êtes éligible pour un nouveau don !");
            }
        }

        $output->writeln("Donation reminders sent.");
        return Command::SUCCESS;
    }
}
