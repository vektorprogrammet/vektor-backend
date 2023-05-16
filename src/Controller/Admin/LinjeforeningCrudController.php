<?php

namespace App\Controller\Admin;

use App\Entity\Linjeforening;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LinjeforeningCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Linjeforening::class;
    }


    public function configureFields(string $pageName): iterable
    {
       yield IdField::new('id')
           ->onlyOnIndex();
       yield TextField::new('name');
       yield AssociationField::new('fieldOfStudy')
           ->setFormTypeOptions([
               'choice_label' => 'name',
           ]);
       yield TextField::new('contact_person');
    }
}
