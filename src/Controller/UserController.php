<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordFormType;
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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
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
    public function create(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Uploader $uploader) : Response {

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

            $user->setIsActif(true);
            $user->setPseudo($userRepository->GeneratePseudo($user->getPrenom()));
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    'password'
                )
            );

            $user->setIsActif(true);
            $user->setFirstConnection(true);
            $user->setRoles(array('ROLE_USER'));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Utilisateur crée");

            return $this->redirectToRoute('user_details', array('id'=> $user->getId()));
        }

        return $this->render('user/create.html.twig', [
            'registrationForm'=> $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function edit(User $user,Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager){
        $userConnect = $this->getUser();

        // si utilisateur connectée cherche a consulter son profil et quil a le role user
        if($userConnect->getId() == $user->getId() && in_array('ROLE_USER', $this->getUser()->getRoles(), true) ||  in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            $userupdate = $user;

            $userForm = $this->createForm(UserFormType::class, $userupdate);

            $userFormUpdate = $request->request->all('user_form');
            // si userFormUpdate <6
         /*   if (!empty($userFormUpdate) &&count($userFormUpdate)<6){
                $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                    ->enableExceptionOnInvalidIndex()
                    ->getPropertyAccessor();

                $userupdate->setPseudo($propertyAccessor->getValue($userFormUpdate, '[pseudo]'));
                $userupdate->setEmail($propertyAccessor->getValue($userFormUpdate, '[email]'));
                $userupdate->setTelephone($propertyAccessor->getValue($userFormUpdate, '[telephone]'));
                $userupdate->setPassword($propertyAccessor->getValue($userFormUpdate, '[password]'));

                $arrayUser = array('nom'=> $userupdate->getNom(),
                    'prenom'=> $userupdate->getPrenom(),
                    'pseudo'=> $userupdate->getPseudo(),
                    'telephone'=> $userupdate->getTelephone(),
                    'photo'=> $userupdate->getPhoto(),
                    'email'=> $userupdate->getEmail(),
                    'is_actif'=> $userupdate->isIsActif(),
                    'site'=> $userupdate->getSite(),
                    'password'=> $userupdate->getPassword(),
                    '_token'=> $propertyAccessor->getValue($userFormUpdate, '[_token]'));
                $request->request->set('user_form', $arrayUser);
            }
*/

            $userForm->handleRequest($request);
            //dd($userForm);

            if($userForm->isSubmitted() && $userForm->isValid()){
                //dd($request);
                /*$user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $userForm->get('password')->getData()
                    )
                );*/
                if(!$userPasswordHasher->isPasswordValid($userConnect, $userForm->get('password')->getData())){
                    $this->addFlash("error", "Mot de passe incorrecte");
                    return $this->render('user/edit.html.twig', [
                        'userForm'=> $userForm->createView(),
                    ]);
                }
                $entityManager->persist($user);
                $entityManager->flush();

                if ($userConnect->getId() == $user->getId()){
                    $this->addFlash("success", "Votre profil a bien été modifié");
                }
                else{
                    $this->addFlash("success", "Le profil de l'utilisateur a bien été modifié");
                }
                return $this->redirectToRoute('user_details',array('id'=> $user->getId()));

            }
        }
        else{
            throw new Exception("Accès refusé", 403);
        }

        return $this->render('user/edit.html.twig', [
            'userForm'=> $userForm->createView(),
        ]);

    }

    #[Route('/changePassword/{id}', name: '_changePassword', requirements: ['id' => '\d+'])]
    public function changePassword(User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository){
        if ($user->getId() != $this->getUser()->getId()){
            throw new Exception("Accès refusé, mon petit", 403);
        }

        //initialisation du formulaire
        $passwordForm = $this->createForm(PasswordFormType::class, $user);
        $passwordForm->handleRequest($request);

        if($passwordForm->isSubmitted() && $passwordForm->isValid()){

            if(!$userPasswordHasher->isPasswordValid($user, $passwordForm->get('password')->getData())){
                $this->addFlash("error", "Ancien mot de passe incorrecte");
                return $this->render('user/changePassword.html.twig', [
                    'passwordForm'=> $passwordForm->createView(),
                ]);
            }
           else if($userPasswordHasher->isPasswordValid($user, $passwordForm->get('plainPassword')->getData())){
               $this->addFlash("error", "Le nouveau mot de passe ne peut pas être identique à l'ancien");
               return $this->render('user/changePassword.html.twig', [
                   'passwordForm'=> $passwordForm->createView(),
               ]);
           }
            else{
                $newPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $passwordForm->get('plainPassword')->getData()
                );
                $user->setPassword($newPassword);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash("success", "Mot de passe changé avec succès");

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('user/changePassword.html.twig', [
            'passwordForm'=> $passwordForm->createView(),
        ]);
    }

    #[Route('/resetPassword/{id}', name: '_resetPassword', requirements: ['id' => '\d+'])]
    public function ResetPassword(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, int $id, EntityManagerInterface $entityManager){
        if ( in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            $user = $userRepository->find($id);
            if(empty($user)){
                throw new Exception("Utilisateur inconnu", 404);
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    'password'
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash("success", "Mot de passe réinitialisé avec succès");
            return $this->redirectToRoute('user_details', array('id'=> $user->getId()));
        }
        else{
            throw new Exception("Accès refusé, ON SE CALME ET DEMI-TOUR !", 403);
        }
    }
}
