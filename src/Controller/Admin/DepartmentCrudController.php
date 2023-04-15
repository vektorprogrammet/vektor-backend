<?php

namespace App\Controller\Admin;

use App\Entity\Department;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DepartmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Department::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('shortName');
        yield TextField::new('name');
        yield TextField::new('city');
        yield TextField::new('address');
        yield TextField::new('latitude')
            ->onlyOnForms();
        yield TextField::new('longitude')
            ->onlyOnForms();
        yield TextField::new('email');
        yield TextField::new('slack_channel')
            ->onlyOnForms();
        yield TextField::new('logoPath')
            ->onlyOnForms();
        yield BooleanField::new('isActive');
    }
}
