<?php

namespace App\Form\Type;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class GenerateMailingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('semester', EntityType::class, array(
                'class' => 'App:Semester',
                'label' => 'Velg semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => true,
            ))
            ->add('department', EntityType::class, array(
                'class' => 'App:Department',
                'label' => 'Velg region',
                'query_builder' => function (DepartmentRepository $dr) {
                    return $dr->queryForActive();
                },
                'required' => true,
            ))
            ->add('type', ChoiceType::class, array(
                'label' => 'Velg type',
                'choices' => array(
                    'Assistent' => 'Assistent',
                    'Team' => 'Team',
                    'Alle' => 'Alle',
                ),
                'required' => true,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Generer',
            ));
    }
}
