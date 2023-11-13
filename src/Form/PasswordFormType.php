<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class,[
                'required'=> true,
                'label'=> 'Ancien mot de passe',
                'mapped'=> false
            ])
             ->add('plainPassword', RepeatedType::class, [
                 'type' => PasswordType::class,
                 'required'=> false,
                 // instead of being set onto the object directly,
                 // this is read and encoded in the controller
                 'mapped' => false,
                 'options' => [
                     'attr' => ['autocomplete' => 'new-password'],
                     'label_attr' => [ 'class' => 'after:content-["*"] after:ml-0.5 after:text-red-500 text-gray-800' ]
                 ],
                 'first_options' => [
                     'label' => 'Choisir un nouveau mot de passe',
                     'constraints' => [
                         new Regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/',
                             'Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule et 1 caractère spécial'),
                         new Length([
                             'min' => 5,
                             'minMessage' => 'Votre mot de passe doit contenir {{ limit }} caractères',
                             // max length allowed by Symfony for security reasons
                             'max' => 4096,
                         ]),
                     ]
                 ],
                 'second_options' => [
                     'label' => 'Confirmer nouveau mot de passe'
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
