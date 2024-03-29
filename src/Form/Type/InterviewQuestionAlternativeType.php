<?php

namespace App\Form\Type;

use App\Entity\InterviewQuestionAlternative;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewQuestionAlternativeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alternative', TextType::class, [
            'label' => false,
            'attr' => ['placeholder' => 'Fyll inn nytt alternativ'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InterviewQuestionAlternative::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'interviewQuestionAlternative';
    }
}
