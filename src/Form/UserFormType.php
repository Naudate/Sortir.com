<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserFormType extends AbstractType
{
    private $tokenStorage;
    private $siteRepository;

    public function __construct(TokenStorageInterface $tokenStorage, SiteRepository $siteRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->siteRepository = $siteRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom', TextType::class, [
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'Dupond'
                ),
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Nom invalide',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Nom invalide',
                        'max' => 255,
                        'maxMessage' => 'Le nom est trop grand',
                    ]),
                ]
            ])
            ->add('prenom', TextType::class,[
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'Jean'
                ),
                'label'=> 'Prénom',
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Prénom invalide',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Prénom invalide',
                        'max' => 255,
                        'maxMessage' => 'Le prénom est trop grand',
                    ]),
                ]
            ])
            ->add('pseudo', TextType::class,[
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'JDupont'
                ),
                'label'=> 'Pseudo',
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Pseudo invalide',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le pseudo pas assez long',
                        'max' => 255,
                        'maxMessage' => 'Le pseudo est trop grand',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'jean.dupond@exemple.com'
                ),
                'label'=> 'Email',
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Adresse mail invalide',
                    ])
                ]

            ])
            ->add('telephone', TelType::class,[
                'label'=> 'Téléphone',
                'required' => false,
                'attr'=> array(
                    'placeholder' => '0123456789'
                ),
                'constraints'=>[
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le numéro de téléphone doit contenir {{ limit }} caractères',
                        'max' => 10,
                        'maxMessage' => 'Le numéro de téléphone doit contenir {{ limit }} caractères',
                    ]),
                ]
            ])
            /*  ->add('avatar', FileType::class, [
                  'label'=> 'Photo de profil',
                  'mapped' => false,
                  'required' => false,
                  'constraints' => [
                      new File([
                          'maxSize' => '2048k',
                          'mimeTypes' => [
                              'image/*',
                          ],
                          'mimeTypesMessage' => 'Veuillez importer un fichier de type image . ',
                      ])
                  ],
              ])*/
           /* ->add('site', EntityType::class,[
                'class'=> Site::class,
                'choice_label'=> 'nom',
                'query_builder' => function (SiteRepository $siteRepository){
                    return $siteRepository->createQueryBuilder("s")->addOrderBy('s.nom');
                }
            ])*/
            ->add('site', ChoiceType::class,[
                //'class'=> Site::class,
                'choice_label'=> 'nom',
                'required'=> false,
                /*'query_builder' => function (SiteRepository $siteRepository){
                   return $siteRepository->createQueryBuilder("s")->addOrderBy('s.nom');
                },*/
                'choices' => $this->getSiteChoices(), // Appel à la méthode pour obtenir les choix
                'placeholder' => 'Sélectionner un site', // Texte affiché pour la valeur par défaut
            ])

            ->add('isActif', CheckboxType::class, [
                'label'=> 'Utilisateur Actif',
                'required'=> false
            ])
            ->add('password', PasswordType::class,[
                'required'=> true,
                'label'=> 'Mot de passe',
                'mapped'=> false
            ])
            ->add('Modifier', SubmitType::class, [
                'label'=> "Modifier",
                'attr' => [
                    'class' => 'rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
                ]
            ])

           ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
        ;


    }
    private function getSiteChoices()
    {
        $sites = $this->siteRepository->findBy([], ['nom' => 'ASC']); // Récupération de tous les sites, triés par nom si nécessaire

        $choices = [];

        // Ajoutez les sites existants à la liste
        foreach ($sites as $site) {
            $choices[$site->getNom()] = $site;
        }

        return $choices;
    }
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $user = $this->tokenStorage->getToken()->getUser();

        if(!in_array('ROLE_ADMIN', $user->getRoles(), true)){
            $data['nom'] =$user->getNom();
            $data['prenom'] =$user->getPrenom();
            $data['site'] =$user->getSite();
            $data['isActif'] =$user->isIsActif();
            $data['roles'] =$user->getRoles();
            $data['photo'] =$user->getPhoto();

            $event->setData($data);
        }

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
