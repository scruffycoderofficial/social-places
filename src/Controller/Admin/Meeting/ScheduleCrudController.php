<?php

namespace BeyondCapable\Controller\Admin\Meeting;

use BeyondCapable\Entity\Admin\Meeting\Schedule;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Class ScheduleCrudController
 *
 * @package App\Controller\Admin
 */
class ScheduleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Schedule::class;
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
