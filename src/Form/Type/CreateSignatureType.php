<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateSignatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Beskrivelse av signatur (Feks. Leder, Vektorprogrammet)',
                'attr' => ['maxlength' => 250],
            ])
            ->add('signature_path', FileType::class, [
                'required' => false,
                'data_class' => null,
                'label' => 'Signaturbilde',
            ])
            ->add('additional_comment', TextareaType::class, [
                'label' => 'Ekstra kommentar (Valgfritt, dukker opp mellom oversikt og underskrift)',
                'required' => false,
                'attr' => [
                    'maxlength' => 500,
                    'rows' => 5,
                    'placeholder' => "La stå tom om ingen kommentar ønskes. \nNB: trykk lagre for at endringer skal tre i kraft.",
                ],
            ]);
    }
}
