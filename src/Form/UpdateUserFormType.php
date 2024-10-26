<?php

namespace App\Form;

use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UpdateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30,
                    'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères',
                    'maxMessage' => 'Votre prénom ne peut excéder {{ limit }} caractères',
                ]),
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom',
                    'class' => 'form-control'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom',
                    'class' => 'form-control'
                ],
                'constraints' => new Length([
                    'min' => 2,
                    'minMessage' => 'Votre nom doit comporter au moins {{ limit }} caractères'
                ])
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'disabled' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse email',
                    'class' => 'form-control'
                ],
                'constraints' => new Email()
            ])
            ->add('adress', TextType::class, [
                'label' => 'Votre adresse :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium form-label'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse',
                    'class' => 'form-control'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre adresse',
                ])
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Votre code postal :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium form-label'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre code postal',
                    'class' => 'form-control'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre code postal',
                ])
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre ville :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium form-label'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre ville',
                    'class' => 'form-control'
                ],
                'constraints' => new NotBlank([
                    'message' => 'Merci d\'indiquer votre ville',
                ])
            ])
            ->add('phone', PhoneNumberType::class, [
                'default_region' => 'FR',
                'format' => PhoneNumberFormat::NATIONAL,
                'label' => 'Votre téléphone :',
                'label_attr' => [
                    'class' => 'lh-label fw-medium form-label'
                ],
                'attr' => [
                    'placeholder' => 'Merci de saisir votre téléphone',
                    'class' => 'form-control'
                ],
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
