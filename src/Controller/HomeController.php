<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository)
    {
        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime;

        $sorties = $sortieRepository->findAll();
        dump($sorties);
        return $this->render('home/index.html.twig', [
            "sorties" => $sorties,
            "dateActuelle"=>$dateActuelle
        ]);
    }
}
