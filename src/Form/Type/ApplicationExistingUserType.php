<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationExistingUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('applicationPractical', ApplicationPracticalType::class, [
            'data_class' => 'App\Entity\Application',
            'teams' => $options['teams'],
        ]);

        $builder->add('preferredSchool', TextType::class, [
            'label' => 'Er det en spesiell skole som du ønsker å besøke igjen?',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Application',
            'teams' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'application';
    }
}
