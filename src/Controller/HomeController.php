<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        SiteRepository $siteRepository)
    {
        $sites = $siteRepository->findAll();
        /*$sorties = $sortieRepository->findAll();
        dump($sorties);
        return $this->render('home/index.html.twig', ["sorties" => $sorties]);*/

        $searchInput = $request->query->get('nom');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $organisateurOnly = $request->query->get('organisateur');
        $voirSortiesPassees = $request->query->get('voirSortiesPassees');
        $selectedSite = $request->query->get('site');

        // Filtrer les sorties en fonction des dates
        $sorties = $sortieRepository->findBetweenDates($dateDebut, $dateFin, $searchInput, $organisateurOnly, $this->getUser(),$selectedSite );
        // Filtrer les sorties en création

        if ($voirSortiesPassees) {
            $sorties = array_filter($sorties, function ($sortie) {
                return $sortie->getDateHeureDebut() < new \DateTime();
            });
        }
        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime;
        return $this->render('home/index.html.twig', [

            "sorties" => $sorties,
            "dateDebut" => $dateDebut,
            "dateFin" => $dateFin,
            "organisateurOnly" => $organisateurOnly,
            "dateActuelle"=>$dateActuelle,
            "sites" => $sites,  // Passer la liste des sites au template
        ]);
    }
}
