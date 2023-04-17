<?php

namespace App\Controller\Admin;

use App\Entity\TeamApplication;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeamApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TeamApplication::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('name');
        yield TextField::new('fieldOfStudy');
        yield TextField::new('yearOfStudy');
        yield TextField::new('motivationText')
            ->hideOnIndex();
        yield TextField::new('biography')
            ->hideOnIndex();
        yield TextField::new('phone');
        yield TextField::new('email');
        yield AssociationField::new('team');

    }
}
