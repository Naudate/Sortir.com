<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{

    private $tokenStorage;
    private $siteRepository;

    private $entityManager;
    public function __construct(TokenStorageInterface $tokenStorage, SiteRepository $siteRepository, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            ->add('telephone', TextType::class,[
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
        ;
    }
    private function getSiteChoices()
    {
        $sites = $this->siteRepository->findBy([], ['nom' => 'ASC']); // Récupération de tous les sites, triés par nom si nécessaire

        $choices = [];

        // Ajoutez votre valeur par défaut
        $defaultSite = new Site();

       /* $defaultSite->setNom('Sélectionner un site');
        $this->entityManager->persist($defaultSite);
        $choices['0'] = $defaultSite;*/

        // Ajoutez les sites existants à la liste
        foreach ($sites as $site) {
            $choices[$site->getNom()] = $site;
        }

        return $choices;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
