<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required'=> true,
                'attr'=> array(
                    'placeholder' => 'Nom de la sortie'
                )
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date de début de la sortie',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                    'min' => (new DateTime())->format('Y-m-d H:i')
                ]
            ])
            ->add('dateHeureFin',DateTimeType::class, [
                'label' => 'Date de fin de la sortie',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                    'min' => (new DateTime())->format('Y-m-d H:i')
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite pour l`\'inscription',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker',
                    'min' => (new DateTime())->format('Y-m-d H:i')
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
            ->add('ville', EntityType::class, [
                'class' => Ville::class, // Remplacez Ville par le nom de votre entité Ville
                'choice_label' => function ($ville) {
                    return $ville->getCodePostal() . ' - ' . $ville->getNom();
                },
                'placeholder' => 'Sélectionnez une ville',
                'mapped' => false,
                'required' => true,
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class, // Remplacez Lieu par le nom de votre entité Lieu
                'placeholder' => 'Sélectionnez d\'abord une ville',
                'choice_label' => function ($lieu) {
                    return $lieu->getNom() . ' - ' . $lieu->getRue();
                },
                'required' => true,
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'name' => 'enregistrer'
                ]
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'name' => 'publier'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
