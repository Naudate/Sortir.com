<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
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
        $registered = $request->query->get('registered');
        $unregistered = $request->query->get('unregistered');
        // Si c'est la première visite (aucun paramètre dans l'URL)
        if ($request->query->count() === 0) {
            $registered = true;
            $unregistered = true;
            $organisateurOnly = true;
        }
        // Filtrer les sorties en fonction des dates
        $sorties = $sortieRepository->findBetweenDates(
            $dateDebut,
            $dateFin,
            $searchInput,
            $organisateurOnly,
            $this->getUser(),
            $selectedSite,
        );
        if ($voirSortiesPassees) {
            $sorties = array_filter($sorties, function ($sortie) {
                return $sortie->getDateHeureDebut() < new \DateTime();
            });
        }
        // Filtrer les sorties en fonction de l'inscription de l'utilisateur
        if ($registered && !$unregistered) {
            $sorties = array_filter($sorties, function ($sortie) {
                return $sortie->getParticipant()->contains($this->getUser());
            });
        }

        if ($unregistered && !$registered) {
            $sorties = array_filter($sorties, function ($sortie) {
                return !$sortie->getParticipant()->contains($this->getUser());
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
            "registered" => $registered, // Ajout de la variable
            "unregistered" => $unregistered, // Ajout de la variable
        ]);
    }
}
