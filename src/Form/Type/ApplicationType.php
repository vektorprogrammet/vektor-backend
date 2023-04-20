<?php

namespace App\Form\Type;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', CreateUserOnApplicationType::class, [
                'label' => '',
                'departmentId' => $options['departmentId'],
            ])
        ->add('yearOfStudy', ChoiceType::class, [
            'label' => 'Årstrinn',
            'choices' => [
                '1. klasse' => '1. klasse',
                '2. klasse' => '2. klasse',
                '3. klasse' => '3. klasse',
                '4. klasse' => '4. klasse',
                '5. klasse' => '5. klasse',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
            'user' => null,
            'allow_extra_fields' => true,
            'departmentId' => null,
            'environment' => 'prod',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'application';
    }
}
