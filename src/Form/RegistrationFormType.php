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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('telephone', NumberType::class,[
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
            ->add('site', EntityType::class,[
                'class'=> Site::class,
                'choice_label'=> 'nom',
                'query_builder' => function (SiteRepository $siteRepository){
                   return $siteRepository->createQueryBuilder("s")->addOrderBy('s.nom');
                }
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
