<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label_attr' => ['class' => 'text-less-grey'],
                'attr' => array(
                    'placeholder' => 'Votre prénom'
                )
            ])
            ->add('lastName', TextType::class, [
                'label_attr' => ['class' => 'text-less-grey'],
                'attr' => array(
                    'placeholder' => 'Votre nom'
                )
            ])            
            ->add('dateOfBirth', DateType::class, [
                'label_attr' => ['class' => 'text-less-grey'],
                'attr' => array(
                    'placeholder' => 'Votre date de naissance'
                )
            ])
            ->add('sexe', ChoiceType::class, [
                'label_attr' => ['class' => 'text-less-grey'],
                'attr' => array(
                    'placeholder' => 'Votre sexe'
                ),
                'choices'  => [
                    'Homme' => 'm',
                    'Femme' => 'f'
                ],
            ])
            ->add('email', EmailType::class, [
                'label_attr' => ['class' => 'text-less-grey'],
                'attr' => array(
                    'placeholder' => 'Votre email'
                )
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label_attr' => ['class' => 'text-less-grey'],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options'  =>
                ['label' => 'Votre mot de passe',
                'label_attr' => ['class' => 'text-less-grey'],
                'row_attr' => [
                    'class' => 'col-md-12 mb-3'
                ],
                'attr' => array(
                    'placeholder' => 'Votre mot de passe'
                )
                ],
                'constraints' => [
                            new NotBlank([
                                'message' => 'Merci d\'entrer un mot de passe',
                            ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Votre mot de passe doit contenir au moins 6 caractères',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]),
                        ],
                'second_options' =>
                ['label' => 'Répétez vôtre mot de passe',
                'label_attr' => ['class' => 'text-less-grey'],
                'row_attr' => [
                    'class' => 'col-md-12 mb-3'
                    ],
                'attr' => array(
                    'placeholder' => 'Répétez votre mot de passe'
                )
                ],
                'invalid_message' => 'Veuillez saisir deux nouveaux mots de passe identiques'

            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
