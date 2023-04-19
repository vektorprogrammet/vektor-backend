<?php

namespace App\Form\Type;

use App\Entity\PasswordReset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class PasswordResetType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordReset::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'passwordReset';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-post',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Valid(),
                ],
            ]);
    }
}
