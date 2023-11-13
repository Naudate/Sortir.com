<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Enum\Etat;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/sortie/', name: 'sortie')]
class SortieController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

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

                return $this->redirectToRoute('create_sortie');
            }
        }

        return $this->render('sortie/create.html.twig', [
            'form'=> $form->createView(),
        ]);}

    #[Route('/register', name: '_register', requirements: ['id' => '\d+'])]
   public function inscription(int $id, EntityManagerInterface $entityManager, SortieRepository $sortieRepository){
        //récupération de l'utilisateur connectée
        $userConnect =  $this->getUser();

        // récupération de la sortie
        $sortie = $sortieRepository->find($id);
        $nbrParticipant = $sortie->getParticipant()->count();

        if ($sortie->getNombreMaxParticipant()> $nbrParticipant){
            //ajout de l'utilisateur connectée à la liste
            $sortie->addParticipant($userConnect);

            // persist des données
            $entityManager->persist($sortie);
            $entityManager->flush();

            //ajout d'un message de succès
            $this->addFlash("success", "Votre inscription à bien été pris en compte");
        }
        else{
            $this->addFlash("error", "Le nombre d'inscription est atteint");

        }



    }


}
