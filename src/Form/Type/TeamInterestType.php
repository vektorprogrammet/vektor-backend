<?php

namespace App\Form\Type;

use App\Entity\Team;
use App\Entity\TeamInterest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamInterestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $builder->getData()->getDepartment();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('potentialTeams', EntityType::class, [
                'label' => 'Hvilke team er du interessert i?',
                'class' => Team::class,
                'query_builder' => fn (EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('team')
                    ->select('team')
                    ->where('team.department = :department')
                    ->andWhere('team.active = true')
                    ->setParameter('department', $department),
                'expanded' => true,
                'error_bubbling' => true,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TeamInterest::class,
            'department' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'App_teaminterest';
    }
}
