<?php

namespace App\Form\Type;

use App\Entity\AccessRule;
use App\Entity\Team;
use App\Entity\User;
use App\Role\Roles;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessRuleType extends AbstractType
{
    private $roles;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->roles = $options['roles'];

        $builder
            ->add('name', TextType::class)
            ->add('resource', TextType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    Roles::TEAM_MEMBER => Roles::TEAM_MEMBER,
                    Roles::TEAM_LEADER => Roles::TEAM_LEADER,
                    Roles::ASSISTANT => Roles::ASSISTANT,
                    Roles::ADMIN => Roles::ADMIN,
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('teams', EntityType::class, [
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Team::class,
                'group_by' => 'department.city',
            ])
            ->add('forExecutiveBoard', CheckboxType::class, [
                'label' => 'Hovedstyret',
                'required' => false,
            ])
            ->add('users', EntityType::class, [
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => User::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('u')
                    ->select('u')
                    ->where('u.roles IN (:roles)')
                    ->orderBy('u.firstName')
                    ->setParameter('roles', $this->roles),
                'group_by' => 'fieldOfStudy.department.city',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AccessRule::class,
            'roles' => [],
        ]);
    }

    public function getName()
    {
        return 'app_bundle_access_rule_type';
    }
}
