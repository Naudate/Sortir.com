<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Enum\Etat;
use App\Form\CancelSortieFormType;
use App\Form\LieuFormType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
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

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $sortie = new Sortie();
        $lieu = new Lieu();
        $form = $this->createForm(SortieType::class, $sortie);
        $formLieu = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);
        $formLieu->handleRequest($request);

        $errorLieu = false;

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $this->em->persist($lieu);
            $this->em->flush();
            $this->addFlash("success", "Lieu créé");
        }else {
            $errorLieu = true;
        }

        if ($form->isSubmitted() && $form->isValid()) {

            if($sortie->getDateHeureDebut() < new \DateTime()){
                $form->get('dateHeureDebut')->addError(new FormError('La date de fin ne peut pas être antérieure à la date du jour.'));
            }

            if($sortie->getDateHeureDebut() < new \DateTime()){
                $form->get('dateLimiteInscription')->addError(new FormError('La date limite d\'inscription ne peut pas être antérieure à la date du jour.'));
            }

            if($sortie->getDateHeureFin() < $sortie->getDateHeureDebut()){
                $form->get('dateHeureFin')->addError(new FormError('La date de fin ne peut pas être antérieure à la date de début.'));
            }

            if($sortie->getDateLimiteInscription() > $sortie->getDateHeureDebut()){
                $form->get('dateLimiteInscription')->addError(new FormError('La date limite d\'inscription ne peut pas être supérieure à la date de début.'));
            }

            if($form->getClickedButton() && 'publier' === $form->getClickedButton()->getName()){
                $sortie->setIsPublish(true);
                $sortie->setEtat(Etat::OUVERT);
            }else{
                $sortie->setIsPublish(false);
                $sortie->setEtat(Etat::EN_CREATION);
            }

            if ($form->getErrors(true)->count() == 0) {

                $sortie->setOrganisateur($this->getUser());
                $this->em->persist($sortie);
                $this->em->flush();
                $this->addFlash("success", "Sortie crée");

                return $this->redirectToRoute('sortie_create');
            }
        }

        return $this->render('sortie/create.html.twig', [
            'form'=> $form->createView(),
            'lieuForm'=> $formLieu->createView(),
            'errorLieu' => $errorLieu
        ]);}


    #[Route('/register/{id}', name: '_register', requirements: ['id' => '\d+'])]
    public function inscription(Sortie $sortie, int $id,EntityManagerInterface $entityManager, SortieRepository $sortieRepository, UserRepository $userRepository){
        //récupération de l'utilisateur connectée
        $userConnect =  $this->getUser();

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

            if($sortie->getNombreMaxParticipant() == $sortie->getParticipant()->count()){
                $sortie->setEtat(Etat::CLOTURE);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            //ajout d'un message de succès
            $this->addFlash("success", "Votre inscription à bien été pris en compte");
        }
        else if ($sortie->getNombreMaxParticipant()== $nbrParticipant){
            $this->addFlash("error", "Le nombre d'inscription est atteint");

        }
        else if ($sortie->getEtat() != Etat::OUVERT){
            $this->addFlash("error", "Il n'est pas possible de s'inscrire à cette sortie");
        }
        else if ($dateActuelle > $sortie->getDateLimiteInscription()){
            $this->addFlash("error", "Il est trop tard pour s'inscrire");
        }
        else{
            $this->addFlash("error", "Impossible de prendre en compte votre candidature");
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/unRegister/{id}', name: '_unregister', requirements: ['id' => '\d+'])]
    public function seDesister (int $id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository){
        //récupération de l"utilisateur connecté
        $userConnect =  $this->getUser();

        // récupération de la sortie
        $sortie = $sortieRepository->find($id);

        date_default_timezone_set('Europe/Paris');
        $dateActuelle = new \DateTime;

        //vérification que la date limite pour se désinscrire est valide
        if($sortie->getEtat() == Etat::OUVERT
            && $sortie->isIsPublish()
            &&  $dateActuelle < $sortie->getDateLimiteInscription()){

            if ($sortie->getParticipant()->contains($userConnect)){
                // suppression de l'utilisateur
                $sortie->removeParticipant($userConnect);

                // si létat de la sortie etait cloturé, il faut faire passé en ouvert
                if ($sortie->getEtat() == Etat::CLOTURE){
                    $sortie->setEtat(Etat::OUVERT);
                }

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash("success", "Votre désinscription à bien été pris en compte");

            }
            else{
                $this->addFlash("error", "Petit malin, eh non, tu n'a jamais fait parti des participants");
            }

            return $this->redirectToRoute('app_home');

        }
    }


    #[Route('/cancel/{id}', name: '_cancel', requirements: ['id' => '\d+'])]
    public function annuler (int $id, Sortie $sortie, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository){

        //récupération de l'utilisateur connectée
        $userConnect =  $this->getUser();
        $form = $this->createForm(CancelSortieFormType::class, $sortie);
        $form->handleRequest($request);

        // vérification que la sortie existe
        if(empty($sortie)){
            throw new Exception("Sortie inconnu", 404);
        }
        // sinon si l'utilisateur connecté est celui qui a origanisé la sortie ou si il est administrateur
        else if ($sortie->getOrganisateur()== $userConnect
                || in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            if ($form->isSubmitted()){
                // persist des données
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

        }
        else {
            throw new Exception("Ne joue pas avec mes nerfs mon petit !",403);
        }

        return $this->render('sortie/cancel.html.twig', [
            'cancelSortieForm'=> $form->createView(),
        ]);
    }


}
