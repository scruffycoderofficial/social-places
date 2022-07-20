<?php

namespace BeyondCapable\Core\Platform\Presenter\Controller\Admin;

use BeyondCapable\Core\Platform\Domain\Entity\Person;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PersonsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Person::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        /**
        return $crud
            ->setDefaultSort(['publishedAt' => 'DESC']);
         *
         */
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('first_name')
            ->add('last_name')
            ->add('email_address');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('first_name');
        yield TextField::new('last_name');
        yield TextField::new('email_address');
        yield TextField::new('phone_number');
    }
}
