<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codePostal', TextType::class, [
                'required'=> true,
                'attr' => [
                    'class' => 'autocompleteCodePostal mt-1 w-full'
                ],
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Le nom ne doit pas être vide',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le code postal doit être sûr 5 caractères',
                        'max' => 5,
                        'maxMessage' => 'Le code postal doit être sûr 5 caractères',
                    ]),
                ]
            ])
            ->add('nom', TextType::class, [
                'required'=> true,
                'attr' => [
                    'class' => 'autocompleteNom mt-1 w-full'
                ],
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Le code postal ne doit pas être vide',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit faire plus de 2 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le nom doit faire moins de 255 caractères',
                    ]),
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
