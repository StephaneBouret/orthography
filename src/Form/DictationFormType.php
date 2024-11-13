<?php

namespace App\Form;

use App\Entity\Courses;
use App\Validator\WordCountConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DictationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dictationText', TextareaType::class, [
                'label' => 'Tapez votre dictée ici',
                'required' => true,
                'attr' => ['rows' => 10],
                'constraints' => [
                    new WordCountConstraint(['courses' => $options['courses']])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'courses' => null,
        ]);

        $resolver->setAllowedTypes('courses', ['null', Courses::class]);
    }
}