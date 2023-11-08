<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Form\DataTransformer\VilleTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'Nom de la sortie'
                ),
                'constraints'=>[
                    new NotBlank([
                        'message' => 'Nom invalide',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit faire plus de 2 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le nom doit faire moins de 255 caractères',
                    ]),
                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date de début de la sortie',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                ]
            ])
            ->add('dateHeureFin',DateTimeType::class, [
                'label' => 'Date de fin de la sortie',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite pour l`\'inscription',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                ]
            ])
            ->add('nombreMaxParticipant', IntegerType::class, [
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'placeholder' => '1',
                ]
            ])
            ->add('description',TextareaType::class, [
                'required' => false,
                'attr'=> array(
                    'placeholder' => 'Description optionnelle',
                    'class' => 'tinymce'
                )
            ])
            ->add('ville', TextType::class, [
                'attr' => ['class' => 'autocomplete'],
            ]);

        $builder->get('ville')
            ->addModelTransformer(new VilleTransformer($this->em));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
