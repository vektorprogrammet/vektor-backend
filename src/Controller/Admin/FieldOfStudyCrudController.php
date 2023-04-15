<?php

namespace App\Controller\Admin;

use App\Entity\FieldOfStudy;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FieldOfStudyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FieldOfStudy::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('shortName');
        yield TextField::new('name');
        yield AssociationField::new('department');
    }
}
