<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Enum\Etat;
use App\Form\CancelSortieFormType;
use App\Form\LieuFormType;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,
                                private SortieRepository $sortieRepository,
                                private LieuRepository $lieuRepository,
                                private VilleRepository $villeRepository)
    {
    }

    #[Route('/create', name: '_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            throw new Exception("Accès refusé", 403);
        }
        $sortie = new Sortie();
        $lieu = new Lieu();
        $form = $this->createForm(SortieType::class, $sortie);
        $formLieu = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);
        $formLieu->handleRequest($request);

        $errorLieu = false;
        $submitFormSortie = false;

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $this->em->persist($lieu);
            $this->em->flush();
            $this->addFlash("success", "Lieu créé");
        }

        if ($formLieu->isSubmitted() && !$formLieu->isValid()) {
            $errorLieu = true;
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $submitFormSortie = true;

            if ($sortie->getDateHeureDebut() < new \DateTime()) {
                $form->get('dateHeureDebut')->addError(new FormError('La date de fin ne peut pas être antérieure à la date du jour.'));
            }

            if ($sortie->getDateLimiteInscription() < new \DateTime()) {
                $form->get('dateLimiteInscription')->addError(new FormError('La date limite d\'inscription ne peut pas être antérieure à la date du jour.'));
            }

            if ($sortie->getDateHeureFin() < $sortie->getDateHeureDebut()) {
                $form->get('dateHeureFin')->addError(new FormError('La date de fin ne peut pas être antérieure à la date de début.'));
            }

            if ($sortie->getDateLimiteInscription() > $sortie->getDateHeureDebut()) {
                $form->get('dateLimiteInscription')->addError(new FormError('La date limite d\'inscription ne peut pas être supérieure à la date de début.'));
            }

            if ($form->getClickedButton() && 'publier' === $form->getClickedButton()->getName()) {
                $sortie->setIsPublish(true);
                $sortie->setEtat(Etat::OUVERT);
            } else {
                $sortie->setIsPublish(false);
                $sortie->setEtat(Etat::EN_CREATION);
            }

            if ($form->getErrors(true)->count() == 0) {

                $sortie->setOrganisateur($this->getUser());
                $this->em->persist($sortie);
                $this->em->flush();
                $this->addFlash("success", "Sortie crée");

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView(),
            'lieuForm' => $formLieu->createView(),
            'errorLieu' => $errorLieu,
            'submitFormSortie' => $submitFormSortie
        ]);
    }


    #[Route('/register/{id}', name: '_register', requirements: ['id' => '\d+'])]
    public function inscription(int $id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, UserRepository $userRepository)
    {
        //récupération de l'utilisateur connectée
        $userConnect = $this->getUser();
        $sortie = $sortieRepository->find($id);

        if(empty($sortie)){
            throw new Exception("Sortie inconnu", 404);
        }
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            throw new Exception("Tu sais, je commence à croire que tu veux casser mon programme !", 403);
        }

        // récupération de la sortie
        $nbrParticipant = $sortie->getParticipant()->count();
        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime;
        //dd($sortie);

        if ($sortie->getNombreMaxParticipant()> $nbrParticipant
                && $sortie->getEtat() == Etat::OUVERT || $sortie->getEtat() == Etat::CLOTURE
                && $sortie->isIsPublish()
                &&  $dateActuelle <= $sortie->getDateLimiteInscription()){

            //ajout de l'utilisateur connectée à la liste
            $sortie->addParticipant($userConnect);

            // persist des données
            $entityManager->persist($sortie);
            $entityManager->flush();
            //dd($sortie);

            if ($sortie->getNombreMaxParticipant() == $sortie->getParticipant()->count()) {
                $sortie->setEtat(Etat::CLOTURE);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            //ajout d'un message de succès
            $this->addFlash("success", "Votre inscription à bien été pris en compte");
        } else if ($sortie->getNombreMaxParticipant() == $nbrParticipant) {
            $this->addFlash("error", "Le nombre d'inscription est atteint");

        } else if ($sortie->getEtat() != Etat::OUVERT) {
            $this->addFlash("error", "Il n'est pas possible de s'inscrire à cette sortie");
        }
        else if ($dateActuelle > $sortie->getDateLimiteInscription()){
            $this->addFlash("error", "Il est trop tard pour s'inscrire");
        } else {
            $this->addFlash("error", "Impossible de prendre en compte votre candidature");
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/unRegister/{id}', name: '_unregister', requirements: ['id' => '\d+'])]
    public function seDesister (int $id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository){
        //récupération de l"utilisateur connecté
        $userConnect = $this->getUser();

        // récupération de la sortie
        $sortie = $sortieRepository->find($id);

        // vérification que la sortie existe
        if(empty($sortie)){
            throw new Exception("Sortie inconnu", 404);
        }
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            throw new Exception("Tu sais, je commence à croire que tu veux casser mon programme !", 403);
        }

        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime;

        //vérification que la date limite pour se désinscrire est valide
        if ( $sortie->isIsPublish()
            && $dateActuelle < $sortie->getDateLimiteInscription() && $sortie->getEtat() == Etat::OUVERT || $sortie->getEtat() == Etat::CLOTURE) {
            if ($sortie->getParticipant()->contains($userConnect)) {
                // suppression de l'utilisateur
                $sortie->removeParticipant($userConnect);

                // si létat de la sortie etait cloturé, il faut faire passé en ouvert
                if ($sortie->getEtat() == Etat::CLOTURE) {
                    $sortie->setEtat(Etat::OUVERT);
                }

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash("success", "Votre désinscription à bien été pris en compte");

            } else {
                $this->addFlash("error", "Petit malin, eh non, tu n'a jamais fait parti des participants");
            }

        }
        return $this->redirectToRoute('app_home');

    }


    #[Route('/cancel/{id}', name: '_cancel', requirements: ['id' => '\d+'])]
    public function annuler (int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository){

        $sortie = $sortieRepository->find($id);
        //récupération de l'utilisateur connectée
        $userConnect =  $this->getUser();
        $form = $this->createForm(CancelSortieFormType::class, $sortie);
        $form->handleRequest($request);
       //dd($form->isValid());

        // vérification que la sortie existe
        if(empty($sortie)){
            throw new Exception("Sortie inconnu", 404);
        }
        else if ($sortie->getEtat() == Etat::ANNULLE){
            throw new Exception("Ne joue pas avec mes nerfs mon petit !", 403);
        }
        // sinon si l'utilisateur connecté est celui qui a origanisé la sortie ou si il est administrateur
        else if ($sortie->getOrganisateur()== $userConnect
                || in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            if ($form->isSubmitted() && $form->isValid()){
                $sortie->setMotif($form->get('motif')->getData());
                $sortie->setEtat(Etat::ANNULLE);
                // persist des données
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash("success", "La sortie a été annulé avec succès");
                return $this->redirectToRoute('app_home');
            }
        }
        else {
            throw new Exception("Ne joue pas avec mes nerfs mon petit !",403);
        }

        return $this->render('sortie/cancel.html.twig', [
            'cancelSortieForm'=> $form->createView(),
            'sortie'=> $sortie
        ]);
    }


    #[Route('/publish/{id}', name: '_publish', requirements: ['id' => '\d+'])]
    public function publish (int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository){

        //récupération de l'utilisateur connecté
        $userConnectee = $this->getUser();

        // récuperation de la sortie
        $sortie = $sortieRepository->find($id);

        // vérification de la sortie
        if(empty($sortie)){
            throw new Exception("Sortie inconnu, petit malin. Je suis invulnérable !", 404);
        }

        // si l'utilisateur à le role admin
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            throw new Exception("Du balai, les fraudeurs n'ont pas leur place ici", 403);
        }
        // si l"utilisateur connecteé est l'organisateur de la sortie
        if ($sortie->getOrganisateur() == $userConnectee && $sortie->getEtat() == Etat::EN_CREATION ){

            // changement de l'état de la sortie
            $sortie->setEtat(Etat::OUVERT);
            $sortie->setIsPublish(true);
            //persist des données
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash("success", "La sortie a été publié avec succès");
            return $this->redirectToRoute('app_home');
        }
        else {
            throw new Exception("Qu'est-ce que tu n'as pas compris dans NOOOOOOONNNNN!",403);
        }
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function details(int $id )
    {
        $sortie = $this->sortieRepository->find($id);

        //Affichage dans la vue information sortie
        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function edit(Request $request, int $id )
    {
        $sortie = $this->sortieRepository->find($id);
        $lieu = $this->lieuRepository->find($sortie->getLieu()->getId());
        $ville = $this->villeRepository->find($lieu->getVille()->getId());

        $newLieu = new Lieu();

        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            throw new Exception("Accès refusé", 403);
        }

        $form = $this->createForm(SortieType::class, $sortie);

        $form->get('ville')->setData($ville);

        $formLieu = $this->createForm(LieuFormType::class, $newLieu);
        $form->handleRequest($request);
        $formLieu->handleRequest($request);

        $errorLieu = false;
        $submitFormSortie = false;

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $this->em->persist($newLieu);
            $this->em->flush();
            $this->addFlash("success", "Lieu créé");
        }

        if ($formLieu->isSubmitted() && !$formLieu->isValid()) {
            $errorLieu = true;
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $submitFormSortie = true;

            if ($sortie->getDateHeureDebut() < new \DateTime()) {
                $form->get('dateHeureDebut')->addError(new FormError('La date de fin ne peut pas être antérieure à la date du jour.'));
            }

            if ($sortie->getDateLimiteInscription() < new \DateTime()) {
                $form->get('dateLimiteInscription')->addError(new FormError('La date limite d\'inscription ne peut pas être antérieure à la date du jour.'));
            }

            if ($sortie->getDateHeureFin() < $sortie->getDateHeureDebut()) {
                $form->get('dateHeureFin')->addError(new FormError('La date de fin ne peut pas être antérieure à la date de début.'));
            }

            if ($sortie->getDateLimiteInscription() > $sortie->getDateHeureDebut()) {
                $form->get('dateLimiteInscription')->addError(new FormError('La date limite d\'inscription ne peut pas être supérieure à la date de début.'));
            }

            if ($form->getClickedButton() && 'publier' === $form->getClickedButton()->getName()) {
                $sortie->setIsPublish(true);
                $sortie->setEtat(Etat::OUVERT);
            } else {
                $sortie->setIsPublish(false);
                $sortie->setEtat(Etat::EN_CREATION);
            }

            if ($form->getErrors(true)->count() == 0) {

                $sortie->setOrganisateur($this->getUser());
                $this->em->persist($sortie);
                $this->em->flush();
                $this->addFlash("success", "Sortie modifiée");

                return $this->redirectToRoute('app_home');
            }

        }

        return $this->render('sortie/update.html.twig', [
            'form' => $form->createView(),
            'lieuForm' => $formLieu->createView(),
            'sortie' => $sortie,
            'errorLieu' => $errorLieu,
            'submitFormSortie' => $submitFormSortie
        ]);
    }
}
