<?php

namespace App\Form\Type;

use App\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-post',
            ])
            ->add('shortDescription', TextType::class, [
                'label' => 'Kort beskrivelse',
                'required' => false,
            ])
            ->add('preview', SubmitType::class, [
                'label' => 'Forhåndsvis',
            ])
            ->add('acceptApplication', CheckboxType::class, [
                'label' => 'Ta i mot søknader?',
                'required' => false,
            ])
            ->add('deadline', DateTimeType::class, [
                'label' => 'Søknadsfrist',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'required' => false,
                'html5' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktivt team',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'createTeam';
    }
}
