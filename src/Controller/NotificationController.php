<?php
namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function index(NotificationService $notificationService): Response
    {
        // Get unread notifications using the service
        $notifications = $notificationService->getUnreadNotifications($this->getUser());

        return $this->render('Client/DonDeSang.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notification/{id}/mark-as-read', name: 'mark_notification_read')]
    public function markAsRead(int $id, NotificationRepository $notificationRepository, NotificationService $notificationService): Response
    {
        // Find the notification using the repository
        $notification = $notificationRepository->find($id);
        
        if ($notification) {
            // Mark the notification as read using the service
            $notificationService->markAsRead($notification);
        }

        // Redirect to the notifications page
        return $this->redirectToRoute('notifications');
    }
}
