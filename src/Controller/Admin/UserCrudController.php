<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('user_name');
        yield TextField::new('firstName');
        yield TextField::new('lastName');
        yield AssociationField::new('fieldOfStudy');
        yield BooleanField::new('gender');
        yield TextField::new('phone');
        yield TextField::new('accountNumber');
        yield TextField::new('password')
            ->hideOnIndex()
            ->hideOnForm()
            ->hideOnDetail();
        yield TextField::new('email');
        yield TextField::new('companyEmail');
        //  yield TextField::new('roles');
        yield TextField::new('picture_path')
            ->hideOnIndex();
        yield BooleanField::new('isActive');
        yield TextField::new('new_user_code')
            ->hideOnIndex();
    }
}
