<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class SortieController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/sortie/create', name: 'create_sortie')]
    public function create(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sortie->setIsPublish(false);

            $this->em->persist($sortie);
            $this->em->flush();

            $this->addFlash("success", "Sortie crée");

            return $this->redirectToRoute('create_sortie');
        }

        return $this->render('sortie/create.html.twig', [
            'form'=> $form->createView(),
        ]);}
    public function getSorties(int $id)
    {
        /* $queryBuilder = $this->createQueryBuilder("p")
     ->where(' p.title like :w')
     ->setParameter(':w', '%'.$where.'%')
     ->getQuery(); // on récupère la requêtes */

        /*   foreach($sorties as $sortie)
           {
               $sortie->setTitle('Mon titre ' . $sortie->getId() ); // on set les différents champs

           }*/

        //return $sorties->getResult();
    }
    #[Route('/', name: 'app_home')]
   public function displayAll(
       EntityManagerInterface $entityManager,
       SortieRepository $sortieRepository)
   {
       $sorties = $sortieRepository->findAll();
       dump($sorties);
       return $this->render('home/index.html.twig', ["sorties" => $sorties]);
   }
    #[Route('/sortie/register', name: 'register_sortie', requirements: ['id' => '\d+'])]
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
