<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserFormType;
use App\Helper\Uploader;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', name: 'user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/list/{page}', name: '_home', defaults: ['page' => 1])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository  $userRepository, int $page = 1): Response
    {
         $users = $userRepository->findUserWithPagination($page);

        $maxPage = ceil($userRepository->count([]) / 8);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users'=> $users,
            'currentPage' => $page,
            'maxPage' => $maxPage
        ]);
    }

    #[Route('/show/{id}', name: '_details', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function details(UserRepository $userRepository, int $id ){
      //Récupération des informations de l'utilisateur connectée`
        $userConnect = $this->getUser();
        // si utilisateur connecter cherche a consulter son profil et quil a le role user
        if($userConnect->getId() == $id && in_array('ROLE_USER', $this->getUser()->getRoles(), true) ){

            // affichage dan sla vie information de l'utilisateur connectee
            return $this->render('user/details.html.twig', [
                'user' => $userConnect
            ]);
        }
        // si pas id utilisateur connectee, récupération information utilisateur
        $user = $userRepository->find($id);
        // si utilisateur existe et que l'utilisateur connectee  a le role admin
        if ( in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true) && !empty($user)){
            //Affichage dans la vue information utilisateur
            return $this->render('user/details.html.twig', [
                'user' => $user
            ]);
        }
        else if (empty($user)){
            throw new Exception("Utilisateur inconnu", 404);
        }
        else {
            throw new Exception("Accès refusé", 403);
        }
    }

    #[Route('/create', name: '_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Uploader $uploader) : Response {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload de l'image
           // $imageUpload = $form->get('avatar')->getData();
            //dd($imageUpload);
          /*  if(!empty($imageUpload) && $imageUpload instanceof UploadedFile){
                $pathAvatar = $uploader->upload($imageUpload,'assets/avatar/', $form->get('pseudo')->getData() );
                $user->setPhoto($pathAvatar);
            }*/
            // encode the plain password
            $user->setIsActif(true);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
           // $user->setIsActif(true);
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

    #[Route('/edit/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(User $user,Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager){

        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){

            if (!empty($userForm->get('plainPassword')->getData())){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $userForm->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Votre profil a bien été modifié");
            return $this->redirectToRoute('user_details',array('id'=> $user->getId()));
        }
        return $this->render('user/edit.html.twig', [
            'userForm'=> $userForm->createView(),
        ]);

    }
}
