<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @codeCoverageIgnore
 */
class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => $options['available_roles'],
                'expanded' => true, // dropdown
                'multiple' => false, // un seul rôle sélectionné
            ]);

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // Transforme array -> string (pour l'affichage)
                    return is_array($rolesArray) && count($rolesArray) > 0 ? $rolesArray[0] : null;
                },
                function ($roleString) {
                    // Transforme string -> array (pour l'entité)
                    return [$roleString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'available_roles' => ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN'],
        ]);
    }
}