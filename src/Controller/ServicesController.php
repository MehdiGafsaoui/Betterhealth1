<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function services(): Response
    {
        return $this->render('client/services.html.twig', [
            'page_title' => 'Services',
        ]);
    }
    #[Route('/donproduit', name: 'app_dondesang')]
    public function dondesang(): Response
    {
        return $this->render('client/donproduit.html.twig', [
            'page_title' => 'Blood Donation',
        ]);
    }
    #[Route('/dondesang', name: 'app_dondesang2')]
    public function dondesang1(): Response
    {
        return $this->render('client/dondesang.html.twig', [
            'page_title' => 'Blood Donation',
        ]);
    }
    #[Route('/makeAppointments', name: 'app_appointments')]
    public function apointments(): Response
    {
        return $this->render('rendezvous/index.html.twig', [
            'page_title' => 'Blood Donation',
        ]);
    }
}
