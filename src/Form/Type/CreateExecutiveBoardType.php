<?php

namespace App\Form\Type;

use App\Entity\ExecutiveBoard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateExecutiveBoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-post (valgfritt)',
                'required' => false,
            ])
            ->add('shortDescription', TextType::class, [
                'label' => 'Kort beskrivelse',
                'required' => false,
            ])
            ->add('preview', SubmitType::class, [
                'label' => 'Forhåndsvis',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExecutiveBoard::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'createExecutiveBoard';
    }
}
