<?php

namespace App\Controller\Admin;

use App\Entity\Semester;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SemesterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Semester::class;
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
