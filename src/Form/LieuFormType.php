<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LieuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'Jardin des plantes'
                ),
                'label'=> 'Nom',
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Nom du lieu invalide',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Le nom du lieu est trop court',
                        'max' => 255,
                        'maxMessage' => 'Le nom du lieu est trop grand',
                    ]),
                ]
            ])
            ->add('rue', TextType::class,[
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'Rue Stanislas Baudry'
                ),
                'label'=> 'Rue',
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Rue du lieu invalide',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Le nom de la rue est trop court',
                        'max' => 255,
                        'maxMessage' => 'Le nom de la rue est trop grand',
                    ]),
                ]
            ])
            ->add('latitude',NumberType::class,[
                'required'=> true,
                'attr'=> array(
                    'placeholder' => '-4634789'
                ),
                'label'=> 'Latitude',
            ])
            ->add('longitude',NumberType::class,[
                'required'=> true,
                'attr'=> array(
                    'placeholder' => '+983489'
                ),
                'label'=> 'Longitude',
            ])
            ->add('ville', EntityType::class,[
                'class'=> Ville::class,
                'label'=> 'Ville ',
                'choice_label'=> 'nom',
                'query_builder' => function (VilleRepository $villeRepository){
                    return $villeRepository->createQueryBuilder("v")->addOrderBy('v.nom');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
