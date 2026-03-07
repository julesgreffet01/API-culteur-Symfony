<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\RoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', options: ['required' => true, 'label' => "Nom d'utilisateur", 'constraints' => [new NotBlank()]])
            ->add('password', options: ['label' => 'Mot de passe', 'required' => true, 'constraints' => [new NotBlank()]])
            ->add('role', EnumType::class, options: [
                'class' => RoleEnum::class,
                'label' => 'Role',
            ])
            ->add('save', SubmitType::class, options: ['label' => 'Enregistrer']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
