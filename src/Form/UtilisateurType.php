<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('email', EmailType::class, [
                'disabled' => $options['is_edit'],
            ])
            ->add('motDePasse', PasswordType::class, [
                'attr' => ['class' => 'password-field'],
                'data' => $options['default_password'] ?? '', 
            ])
            ->add('motDePasseConfirmation', PasswordType::class, [
                'mapped' => false,
                'attr' => ['class' => 'password-field'],
                'data' => $options['default_password'] ?? '', 
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'nom',
                'placeholder' => 'Select a role',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'constraints' => [
                new Assert\Callback([$this, 'validatePasswords']),
            ],
            'is_edit' => false,
            'default_password' => '123456', 
        ]);
    }

    public function validatePasswords(Utilisateur $utilisateur, ExecutionContextInterface $context)
    {
        $form = $context->getRoot();
        $motDePasse = $form->get('motDePasse')->getData();
        $motDePasseConfirmation = $form->get('motDePasseConfirmation')->getData();

        // Vérifiez uniquement si les deux champs sont présents
        if ($motDePasse && $motDePasseConfirmation && $motDePasse !== $motDePasseConfirmation) {
            $context->buildViolation('Les mots de passe doivent correspondre.')
                ->atPath('motDePasseConfirmation')
                ->addViolation();
        }
    }
}
