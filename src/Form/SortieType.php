<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
                    'class' => 'datetimepicker mt-1 w-full',
                    'min' => (new DateTime())->format('Y-m-d H:i')
                ]
            ])
            ->add('dateHeureFin',DateTimeType::class, [
                'label' => 'Date de fin de la sortie',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker mt-1 w-full',
                    'min' => (new DateTime())->format('Y-m-d H:i')
                ]
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite pour l\'inscription',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'datetimepicker mt-1 w-full',
                    'min' => (new DateTime())->format('Y-m-d H:i')
                ]
            ])
            ->add('nombreMaxParticipant', IntegerType::class, [
                'label' => 'Nombre maximum de participants',
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
                    'class' => 'tinymce mt-1 w-full'
                )
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => function ($site) {
                    return $site->getNom();
                },
                'placeholder' => 'Sélectionner un site',
                'mapped' => true,
                'required' => true,
                'data' => $this->security->getUser()->getSite()
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => function ($ville) {
                    return $ville->getCodePostal() . ' - ' . $ville->getNom();
                },
                'placeholder' => 'Sélectionner une ville',
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
                    'name' => 'enregistrer',
                    'class' => 'rounded-md bg-amber-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600',
                ]
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'name' => 'publier',
                    'class' => 'rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
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
