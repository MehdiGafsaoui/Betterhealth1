<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CentreDeDonRepository;

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
    #[Route('/map', name: 'app_map')]
    public function maps(CentreDeDonRepository $centreDeDonRepository): Response
    {
        $centres = $centreDeDonRepository->findAll();

        return $this->render('centre_de_don/map.html.twig', [
            'page_title' => 'Blood Donation',
            'centres' => $centres,  
        ]);
    }
    #[Route('/cards', name: 'app_card')]
    public function cards(): Response
    {
        return $this->render('demande_don_sang/view.html.twig', [
            'page_title' => 'Blood Donation',
        ]);
    }
}
