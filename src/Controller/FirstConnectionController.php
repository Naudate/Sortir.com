<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FirstConnectionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class FirstConnectionController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    #[Route('/first/connection', name: 'first_connection')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $userBase = clone $this->getUser();
        $pseudoBase = $userBase->getPseudo();
        $form = $this->createForm(FirstConnectionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $submittedPseudo = $form->get('pseudo')->getData();
            $submittedActualPassword = $form->get('actualPassword')->getData();
            $submittedNewPassword = $form->get('plainPassword')->getData();

            if ($pseudoBase == $submittedPseudo) {
                // Générer une erreur personnalisée sur le champ 'pseudo'
                $form->get('pseudo')->addError(new FormError('Le pseudo ne doit pas être le même.'));
            }

            if(!$this->userPasswordHasher->isPasswordValid($user, $submittedActualPassword)){
                $form->get('actualPassword')->addError(new FormError('Le mot de passe actuel ne correspond pas.'));
            }

            if ($this->userPasswordHasher->isPasswordValid($user, $submittedNewPassword)){
                // Générer une erreur personnalisée sur le champ 'pseudo'
                $form->get('plainPassword')->addError(new FormError('Le nouveau mot de passe ne doit pas être le même que l\'ancien.'));
            }

            if ($form->getErrors(true)->count() == 0){
                $newPasswordHash = $this->userPasswordHasher->hashPassword(
                    $user,
                    $submittedNewPassword
                );
                $user->setPassword($newPasswordHash);
                $user->setFirstConnection(false);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('first_connection/index.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
