<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/lieu', name: 'lieu')]
#[IsGranted('ROLE_USER')]
class LieuController extends AbstractController
{
    #[Route('/', name: '_home')]
    public function index(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    #[Route('/show/{id}', name: '_details', requirements: ['id'=> '\d+'])]
    public function details(LieuRepository $lieuRepository, int $id):Response
    {
        $lieu = $lieuRepository->find($id);
        return $this->render('lieu/details.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $entityManager):Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isEmpty()){

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Nouveau lieu créé');
            return $this->redirectToRoute('lieu_details', array('id'=> $lieu->getId()));
        }

        return $this->render('lieu/create.html.twig', [
            'lieuForm'=> $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: '_edit', requirements: ['id'=> '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, EntityManagerInterface $entityManager, LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/edit.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
}
