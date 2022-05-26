<?php

namespace App\Controller\Admin\Meeting;

use App\Entity\Admin\Meeting\Meeting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Class MeetingCrudController
 *
 * @package App\Controller\Admin
 */
class MeetingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Meeting::class;
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
