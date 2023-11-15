<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Form\UserFormType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, private LieuRepository $lieuRepository)
    {
    }

    #[Route('/{page}', name: '_home', defaults: ['page' => 1])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(int $page=1, LieuRepository $lieuRepository): Response
    {
        $lieus = $lieuRepository->findlieuWithPagination($page);

        $maxPage = ceil($lieuRepository->count([]) / 8);


        return $this->render('lieu/index.html.twig', [
            'lieus'=> $lieus,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }

    #[Route('/show/{id}', name: '_details', requirements: ['id'=> '\d+'])]
    public function details(int $id, LieuRepository $lieuRepository):Response
    {
        // vérification du role
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            throw  new \Exception('J\'aurais pu te laisser, mais comment te dire que NOOOOONNNNNN', 403);
        }
        // récupération du lieu
        $lieu = $this->lieuRepository->find($id);

        if (empty($lieu)){
            throw  new \Exception('Tient, tient, un leiu qui n\'existe pas !', 404);
        }

        return $this->render('lieu/details.html.twig', [
            'lieu' => $lieu
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request):Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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
    public function edit(int $id, EntityManagerInterface $entityManager,Request $request, LieuRepository $lieuRepository): Response
    {

        // vérification du role
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            throw  new \Exception('J\'aurais pu te laisser, mais comment te dire que NOOOOONNNNNN', 403);
        }

        $userConnect = $this->getUser();

        $lieu= $lieuRepository->find($id);

        if (empty($lieu)){
            throw  new \Exception('Tient, tient, un lieu qui n\'existe pas !', 404);
        }

        $lieuForm = $this->createForm(LieuFormType::class, $lieu);

        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){

            $this->entityManager->persist($lieu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le lieu à été modifié avec succès');
            return $this->redirectToRoute('lieu_details', array('id'=> $lieu->getId()));
        }

        return $this->render('lieu/edit.html.twig', [
            'lieuForm' => $lieuForm
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id'=> '\d+'])]
    public function delete(int $id, LieuRepository $lieuRepository, EntityManagerInterface $entityManager):Response
    {
        // vérification du role
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            throw  new \Exception('J\'aurais pu te laisser, mais comment te dire que NOOOOONNNNNN', 403);
        }
        // récupération du lieu
        $lieu = $this->lieuRepository->find($id);

        if (empty($lieu)) {
            throw  new \Exception('Tient, tient, un leiu qui n\'existe pas !', 404);
        }

        // suppression du lieu
        $this->entityManager->remove($lieu);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le lieu à été supprimé avec succès');
        return $this->redirectToRoute('lieu_home');
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

    #[Route('/getByLieu', name: '_getByLieu', methods: "POST")]
    public function getByLieu(Request $request)
    {
        $lieu = $request->request->get('lieu');

        $lieux = $this->lieuRepository->findBy(['id' => $lieu]);
        $lieuxArray = [];
        foreach ($lieux as $lieu) {
            $lieuxArray[] = [
                'id' => $lieu->getId(),
                'nom' => $lieu->getNom(),
                'rue' => $lieu->getRue(),
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude(),
                'ville' => [
                    'id' => $lieu->getVille()->getId(),
                    'codePostal' => $lieu->getVille()->getCodePostal(),
                    'nom' => $lieu->getVille()->getNom(),
            ]
            ];
        }

        return new JsonResponse($lieuxArray);
    }

}
