<?php

namespace App\Form\Type;

use App\Entity\FieldOfStudy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldOfStudyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Fullt navn',
                'attr' => [
                    'placeholder' => 'Eks: Datateknikk',
                ],
            ])
            ->add('shortName', TextType::class, [
                'label' => 'Forkortelse',
                'attr' => [
                    'placeholder' => 'Eks: MTDT',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FieldOfStudy::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_bundle_field_of_study_type';
    }
}
