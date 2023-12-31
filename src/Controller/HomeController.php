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
use Knp\Component\Pager\PaginatorInterface;


class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        SiteRepository $siteRepository,
        PaginatorInterface $paginator)
    {
        $sites = $siteRepository->findAll();

        $searchInput = $request->query->get('nom');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $organisateurOnly = $request->query->get('organisateur');
        $voirSortiesPassees = $request->query->get('voirSortiesPassees');
        $selectedSite = $request->query->get('site');
        $registered = $request->query->get('registered');
        $unregistered = $request->query->get('unregistered');
        $selectedState = $request->query->get('etat');

        // Si c'est la première visite (aucun paramètre dans l'URL)
        if ($request->query->count() === 0) {
            $registered = true;
            $unregistered = true;
            $organisateurOnly = true;
            if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
                return $this->redirectToRoute('app_home',
                    array('nom' => $searchInput ?? '',
                        'dateDebut' => $dateDebut ?? '',
                        'dateFin' => $dateFin ?? '',
                        'organisateur' => $organisateurOnly ?? '',
                        'voirSortiesPassees' => $voirSortiesPassees ?? '',
                        'site' => $selectedSite ?? ($this->getUser()->getSite()->getId() ?? ''),
                        'registered' => $registered ?? '',
                        'unregistered' => $unregistered ?? ''
                    ));
            }else{
                return $this->redirectToRoute('app_home',
                    array('nom' => $searchInput ?? '',
                        'dateDebut' => $dateDebut ?? '',
                        'dateFin' => $dateFin ?? '',
                        'site' => $selectedSite ?? '',
                        'etat' => $selectedState ?? ''
                    ));
            }
        }
        // Filtrer les sorties en fonction des dates
        $sorties = $sortieRepository->findBetweenDates(
            $dateDebut,
            $dateFin,
            $searchInput,
            $organisateurOnly,
            $this->getUser(),
            $selectedSite,
            $selectedState,
            $voirSortiesPassees
        );

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


        $pagination = $paginator->paginate(
            $sorties, // Requête contenant les données
            $request->query->get('page', 1), // Numéro de la page. La valeur par défaut est 1.
            4 // Nombre d'éléments par page
        );

        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime;

        return $this->render('home/index.html.twig', [

            "sorties" => $sorties,
            "dateDebut" => $dateDebut,
            "dateFin" => $dateFin,
            "organisateurOnly" => $organisateurOnly,
            "dateActuelle"=>$dateActuelle,
            "sites" => $sites,
            "voirSortiesPassees" => $voirSortiesPassees,
            "registered" => $registered,
            "unregistered" => $unregistered,
            "pagination" => $pagination,
        ]);
    }
}
