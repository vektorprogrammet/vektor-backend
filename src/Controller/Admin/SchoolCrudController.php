<?php

namespace App\Controller\Admin;

use App\Entity\School;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SchoolCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return School::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('name'),
            TextField::new('contactPerson'),
            TextField::new('email'),
            TextField::new('phone'),
            BooleanField::new('active'),
            BooleanField::new('international'),
            AssociationField::new('department'),
        ];
    }
}
