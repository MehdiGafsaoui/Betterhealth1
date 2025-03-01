<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;
use App\Entity\User;

class NotificationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createNotification(User $user, string $message): void
    {
        $notification = new Notification($user, $message);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function getUnreadNotifications(User $user): array
    {
        return $this->entityManager->getRepository(Notification::class)
            ->findBy(['user' => $user, 'isRead' => false], ['createdAt' => 'DESC']);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }
}
