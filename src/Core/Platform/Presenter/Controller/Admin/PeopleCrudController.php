<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Controller\Admin;

use BeyondCapable\Core\Platform\Domain\Entity\Person;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PeopleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Person::class;
    }
}