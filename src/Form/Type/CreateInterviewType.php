<?php

namespace App\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interviewer', EntityType::class, array(
            'class' => 'App:User',
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('u')
                    ->select('u')

                    ->where("u.roles = :_admin")
                    ->orWhere("u.roles = :_teamleader")
                    ->orWhere("u.roles = :_teammember")
                    ->setParameter("_admin", "[\"ROLE_ADMIN\"]")
                    ->setParameter("_teamleader", "[\"ROLE_TEAM_LEADER\"]")
                    ->setParameter("_teammember", "[\"ROLE_TEAM_MEMBER\"]")
                    ->orderBy('u.firstName');
                    //->setParameter('roles', $options['roles']);
            },
            'group_by' => 'fieldOfStudy.department.city',
        ));

        $builder->add('interviewSchema', EntityType::class, array(
            'class' => 'App:InterviewSchema',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('i')
                    ->select('i')
                    ->orderBy('i.id', 'DESC');
            },
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Interview',
            'roles' => [],
        ));
    }

    public function getBlockPrefix()
    {
        return 'interview';
    }
}
