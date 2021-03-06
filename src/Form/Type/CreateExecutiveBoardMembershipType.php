<?php

namespace App\Form\Type;

use App\Repository\SemesterRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateExecutiveBoardMembershipType extends AbstractType
{
    private $departmentId;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->departmentId = $options['departmentId'];

        $builder
            ->add('user', EntityType::class, array(
                'label' => 'Bruker',
                'class' => 'App:User',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->Join('u.fieldOfStudy', 'fos')
                        ->Join('fos.department', 'd')
                        ->where('d = :department')
                        ->setParameter('department', $this->departmentId)
                        ->addOrderBy('u.firstName', 'ASC');
                },
                'choice_label' => function ($value, $key, $index) {
                    return $value->getFullName();
                },
            ))
            ->add('positionName', TextType::class, array(
                'label' => 'Stilling',
            ))
            ->add('startSemester', EntityType::class, array(
                'label' => 'Start semester',
                'class' => 'App:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
            ))
            ->add('endSemester', EntityType::class, array(
                'label' => 'Slutt semester (Valgfritt)',
                'class' => 'App:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ExecutiveBoardMembership',
            'departmentId' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'createExecutiveBoardMembership';
    }
}
