<?php

namespace App\Form\Type;

use App\Entity\AssistantHistory;
use App\Entity\School;
use App\Entity\Semester;
use App\Repository\SemesterRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAssistantHistoryType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];
        $builder
            ->add('Semester', EntityType::class, [
                'label' => 'Semester',
                'class' => Semester::class,
                'query_builder' => fn (SemesterRepository $sr) => $sr->queryForAllSemestersOrderedByAge(),
            ])
            ->add('workdays', ChoiceType::class, [
                'label' => 'Antall uker (4 ganger = 4 uker, 2 ganger i uken i 4 uker = 8 uker)',
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ],
            ])
            ->add('School', EntityType::class, [
                'label' => 'Skole',
                'class' => School::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('s')
                    ->orderBy('s.name', 'ASC')
                    ->JOIN('s.department', 'd')
                    ->where(':department = s.department')
                    ->andWhere('s.active = true')
                    ->setParameter('department', $this->department),
            ])
            ->add('bolk', ChoiceType::class, [
                'label' => 'Bolk',
                'choices' => [
                    'Bolk 1' => 'Bolk 1',
                    'Bolk 2' => 'Bolk 2',
                    'Bolk 1 og Bolk 2' => 'Bolk 1, Bolk 2',
                ],
            ])
            ->add('day', ChoiceType::class, [
                'label' => 'Dag',
                'choices' => [
                    'Mandag' => 'Mandag',
                    'Tirsdag' => 'Tirsdag',
                    'Onsdag' => 'Onsdag',
                    'Torsdag' => 'Torsdag',
                    'Fredag' => 'Fredag',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opprett',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssistantHistory::class,
            'department' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'createAssistantHistory';
    }
}
