<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, private LieuRepository $lieuRepository)
    {
    }

    #[Route('/', name: '_home')]
    public function index(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    #[Route('/show/{id}', name: '_details', requirements: ['id'=> '\d+'])]
    public function details(int $id):Response
    {
        $lieu = $this->lieuRepository->find($id);
        return $this->render('lieu/details.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request):Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isEmpty()){

            $this->entityManager->persist($lieu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Nouveau lieu créé');
            return $this->redirectToRoute('lieu_details', array('id'=> $lieu->getId()));
        }

        return $this->render('lieu/create.html.twig', [
            'lieuForm'=> $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: '_edit', requirements: ['id'=> '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request): Response
    {
        return $this->render('lieu/edit.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    #[Route('/getByVille', name: '_getByVille', methods: "POST")]
    public function getByVille(Request $request)
    {
        $ville = $request->request->get('ville');

        $lieux = $this->lieuRepository->findBy(['ville' => $ville] , ['nom' => 'ASC']);
        $lieuxArray = [];
        foreach ($lieux as $lieu) {
            $lieuxArray[] = [
                'id' => $lieu->getId(),
                'nom' => $lieu->getNom(),
                'rue' => $lieu->getRue(),
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude()
            ];
        }

        return new JsonResponse($lieuxArray);
    }
}
