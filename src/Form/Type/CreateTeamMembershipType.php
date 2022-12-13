<?php

namespace App\Form\Type;

use App\Repository\PositionRepository;
use App\Repository\SemesterRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTeamMembershipType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];

        $builder
            ->add('user', EntityType::class, [
                'label' => 'Bruker',
                'class' => 'App:User',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->orderBy('u.firstName', 'ASC')
                        ->Join('u.fieldOfStudy', 'fos')
                        ->Join('fos.department', 'd')
                        ->where('u.fieldOfStudy = fos.id')
                        ->andWhere('fos.department = d')
                        ->andWhere('d = ?1')
                        ->setParameter(1, $this->department);
                },
            ])
            ->add('isTeamLeader', ChoiceType::class, [
                'choices' => [
                    'Medlem' => false,
                    'Leder' => true,
                ],
                'expanded' => true,
                'label' => false,
            ])
            ->add('position', EntityType::class, [
                'label' => 'Stillingstittel',
                'class' => 'App:Position',
                'query_builder' => function (PositionRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
            ])
            ->add('startSemester', EntityType::class, [
                'label' => 'Start semester',
                'class' => 'App:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
            ])
            ->add('endSemester', EntityType::class, [
                'label' => 'Slutt semester (Valgfritt)',
                'class' => 'App:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Legg til',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\TeamMembership',
            'department' => null
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'createTeamMembership';
    }
}
