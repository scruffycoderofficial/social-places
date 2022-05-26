<?php

namespace BeyondCapable\Controller\Admin\Meeting;

use BeyondCapable\Entity\Admin\Meeting\Request;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Class RequestCrudController
 *
 * @package App\Controller\Admin
 */
class RequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Request::class;
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
