<?php

namespace App\Controller\Admin;

use App\Entity\FieldOfStudy;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FieldOfStudyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FieldOfStudy::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
