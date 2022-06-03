<?php

namespace BeyondCapable\Application\Controller\Admin;

use BeyondCapable\Platform\Domain\Entity\Admin\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * Class ContactCrudController
 *
 * @package App\Controller\Admin
 */
class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
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
