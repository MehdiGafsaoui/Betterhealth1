<?php

// src/Controller/ChatbotController.php
namespace App\Controller;

use App\Repository\EvenementsRepository;
use App\Repository\CategorieevenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/chatbot')]
class ChatbotController extends AbstractController
{
    #[Route('/query', name: 'chatbot_query', methods: ['POST'])]
    public function chatbotResponse(Request $request, EvenementsRepository $evenementsRepository, CategorieevenementRepository $categorieRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = strtolower($data['message'] ?? '');

        // ✅ 1️⃣ Check for event duration queries
        if (preg_match('/how long does the event (.+) last\?|what is the duration of (.+)\?|tell me how many days (.+) will last\?/i', $userMessage, $matches)) {
            $eventName = trim($matches[1] ?? $matches[2] ?? $matches[3]);

            $event = $evenementsRepository->findOneBy(['nom' => $eventName]);

            if ($event && $event->getDatedebut() && $event->getDatefin()) {
                $duration = $event->getDatedebut()->diff($event->getDatefin())->days;
                return new JsonResponse(['response' => "The event '$eventName' lasts for $duration days."]);
            }

            return new JsonResponse(['response' => "I couldn't find the duration for the event '$eventName'."]);
        }

        // ✅ 2️⃣ Check for event queries this week
        if (str_contains($userMessage, 'this week')) {
            $startDate = new \DateTime();
            $endDate = (new \DateTime())->modify('+7 days');
            $events = $evenementsRepository->createQueryBuilder('e')
                ->where('e.Datedebut BETWEEN :start AND :end')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult();

            if (count($events) === 0) {
                return new JsonResponse(['response' => "There are no events happening this week."]);
            }

            $eventList = array_map(fn($e) => $e->getNom() . ' on ' . $e->getDatedebut()->format('Y-m-d'), $events);
            return new JsonResponse(['response' => "Here are this week's events: " . implode(', ', $eventList)]);
        }

        // ✅ 3️⃣ Check for category-based queries
        if (preg_match('/events in category (.+)/', $userMessage, $matches)) {
            $categoryName = trim($matches[1]);
            $category = $categorieRepository->findOneBy(['nom' => $categoryName]);

            if (!$category) {
                return new JsonResponse(['response' => "I couldn't find any events in the '$categoryName' category."]);
            }

            $events = $evenementsRepository->findBy(['categorie' => $category]);

            if (empty($events)) {
                return new JsonResponse(['response' => "No events found in the '$categoryName' category."]);
            }

            $eventList = array_map(fn($e) => $e->getNom(), $events);
            return new JsonResponse(['response' => "Events in '$categoryName': " . implode(', ', $eventList)]);
        }

        // ✅ 4️⃣ Check for event location queries
        if (preg_match('/where is the event (.+)/', $userMessage, $matches)) {
            $eventName = trim($matches[1]);
            $event = $evenementsRepository->findOneBy(['nom' => $eventName]);

            if (!$event) {
                return new JsonResponse(['response' => "I couldn't find an event named '$eventName'."]);
            }

            return new JsonResponse(['response' => "The event '$eventName' is taking place at " . $event->getLieu() . "."]);
        }

        return new JsonResponse(['response' => "Sorry, I don't understand that question. Try asking about events this week, by category, or their locations."]);
    }
}
