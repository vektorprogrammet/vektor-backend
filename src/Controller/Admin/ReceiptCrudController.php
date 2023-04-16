<?php

namespace App\Controller\Admin;

use App\Entity\Receipt;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReceiptCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Receipt::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('visual_id')
            ->hideOnForm();
        yield AssociationField::new('user')
            ->hideOnForm();
        yield TextField::new("description");
        yield NumberField::new('sum');
        yield TextField::new("picturePath")
            ->hideOnIndex();
        yield TextField::new('status');
        yield DateTimeField::new('submitDate')
            ->setFormat('dd.MM.yyyy HH:mm')
            ->hideOnIndex();
        yield DateTimeField::new('receiptDate')
            ->setFormat('dd.MM.yyyy HH:mm')
            ->hideOnIndex();
        yield DateTimeField::new('refundDate')
            ->setFormat('dd.MM.yyyy HH:mm')
            ->hideOnIndex();
    }
}
