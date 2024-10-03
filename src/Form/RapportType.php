<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date',
                'attr' => ['class' => 'form-control'], // Optional: Add Bootstrap class or custom styling
            ])
            ->add('operationType', ChoiceType::class, [
                'choices' => [
                    'Addition' => 'add',
                    'Soustraction' => 'subtract',
                ],
                'required' => false,
                'placeholder' => 'Choisissez un type d\'opération',
                'label' => 'Type d\'opération',
                'attr' => ['class' => 'form-control'], // Optional: Add Bootstrap class or custom styling
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'reference', // Ensure 'reference' is a valid property in Article entity
                'required' => false,
                'placeholder' => 'Tous les articles',
                'multiple' => true, // Allow multiple selections
                'expanded' => true, // Optional: Use checkboxes for multiple selections
                'label' => 'Articles',
                'attr' => ['class' => 'form-control'], // Optional: Add Bootstrap class or custom styling
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, 
        ]);
    }
}
