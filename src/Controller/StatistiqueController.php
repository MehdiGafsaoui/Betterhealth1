<?php

namespace App\Controller;

use App\Repository\DemandeDonSangRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    #[Route('/statistiques', name: 'statistiques')]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('demande_don_sang/index.html.twig');
    }

    #[Route('/api/statistiques/dons-par-mois', name: 'dons_par_mois')]
    public function donsParMois(DemandeDonSangRepository $repository): JsonResponse
    {
        $data = $repository->countDonsByMonth();
        return new JsonResponse($data);
    }

    #[Route('/api/statistiques/dons-par-groupe', name: 'dons_par_groupe')]
    public function donsParGroupe(DemandeDonSangRepository $repository): JsonResponse
    {
        $data = $repository->countDonsByGroupeSanguin();
        return new JsonResponse($data);
    }

    #[Route('/api/statistiques/dons-par-centre', name: 'dons_par_centre')]
    public function donsParCentre(DemandeDonSangRepository $repository): JsonResponse
    {
        $data = $repository->countDonsByCentre();
        return new JsonResponse($data);
    }
}
