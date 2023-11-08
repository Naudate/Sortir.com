<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Helper\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    #[Route('/', name: '_home')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/create', name: '_create')]
    public function Create(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Uploader $uploader) : Response {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload de l'image
            $imageUpload = $form->get('avatar')->getData();
            //dd($imageUpload);
          /*  if(!empty($imageUpload) && $imageUpload instanceof UploadedFile){
                $pathAvatar = $uploader->upload($imageUpload,'assets/avatar/', $form->get('pseudo')->getData() );
                $user->setPhoto($pathAvatar);
            }*/
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsActif(true);
            $user->setRoles(array('ROLE_USER'));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Utilisateur crée");

            return $this->redirectToRoute('user_home');
        }

        return $this->render('user/create.html.twig', [
            'registrationForm'=> $form->createView(),
        ]);
    }
}
