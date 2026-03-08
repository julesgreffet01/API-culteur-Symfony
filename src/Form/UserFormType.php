<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\RoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $builder->getData();

        $builder->add('username', null, [
            'required' => true,
            'label' => "Nom d'utilisateur",
            'constraints' => [new NotBlank()],
            'row_attr' => ['class' => 'form-group']
        ]);

        $builder->add('name', null, [
            'required' => true,
            'label' => "Nom de la personne",
            'constraints' => [new NotBlank()],
            'row_attr' => ['class' => 'form-group']
        ]);

        if (!$user || !$user->getId()) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'constraints' => [new NotBlank()],
                'row_attr' => ['class' => 'form-group']
            ]);
        }

        $builder->add('role', EnumType::class, [
            'class' => RoleEnum::class,
            'label' => 'Rôle',
            'row_attr' => ['class' => 'form-group']
        ]);

        $builder->add('save', SubmitType::class, [
            'label' => 'Enregistrer',
            'attr' => ['class' => 'btn btn-new']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
