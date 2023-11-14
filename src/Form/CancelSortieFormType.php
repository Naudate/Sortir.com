<?php

namespace App\Form;

use App\Entity\Sortie;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CancelSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motif', TextareaType::class,[
                'label'=> 'Motif',
                'attr'=> array(
                    'placeholder'=> 'Indiquer un motif d\'annulation de la sortie'
                ),
                'required'=> true,
                'constraints'=> [
                    new  NotBlank([
                        'message'=> 'Veuillez saisir un motif pour annuler la sortie'
                    ]),
                    new Length([
                        'min' => 7,
                        'minMessage' => 'Le motif doit faire au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Vous avez dépassé la taille autorisé pour ajouter un motif',
                    ])
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=> 'Valider'
            ])
            //->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);

        ;
    }
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        //récupération de la sortie
        $sortie = $form->getData();
        $data['nom'] =$sortie->getNom();
        $data['id'] =$sortie->getId();
        $data['dateHeureDebut'] =$sortie->getDateHeureDebut();
        $data['dateHeureFin'] =$sortie->getDateHeureFin();
        $data['dateLimiteInscription'] =$sortie->getDateLimiteInscription();
        $data['nombreMaxParticipant'] =$sortie->getNombreMaxParticipant();
        $data['description'] =$sortie->getDescription();
        $data['isPublish'] =$sortie->isIsPublish();
        $data['lieu'] =$sortie->getLieu();
        $data['organisateur'] =$sortie->getOrganisateur();
        $data['participant'] =$sortie->getParticipant();
        $data['etat'] =$sortie->getEtat();
        $data['site'] =$sortie->getSite();

        //dd($data);

        $event->setData($data);


    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
