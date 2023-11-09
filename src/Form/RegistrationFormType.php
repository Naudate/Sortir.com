<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
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
            ->add('site', EntityType::class,[
                'class'=> Site::class,
                'choice_label'=> 'nom',
                'query_builder' => function (SiteRepository $siteRepository){
                   return $siteRepository->createQueryBuilder("s")->addOrderBy('s.nom');
                }
            ])
            ->add('isActif', CheckboxType::class, [
                'label'=> 'Utilisateur Actif  ? ',
                'required'=> false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Mot de passe :',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'first_options' => [
                    'label' => 'Choisir un mot de passe',
                    'constraints' => [
                        new Regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/',
                            'Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule et 1 caractère spécial'),
                        new NotBlank([
                            'message' => 'Veuillez saisir un mot de passe',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe'
                ],
                'invalid_message' => 'Les mot de passe ne sont pas identiques.',

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
