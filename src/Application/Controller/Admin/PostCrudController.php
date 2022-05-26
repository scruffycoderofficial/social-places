<?php

namespace BeyondCapable\Application\Controller\Admin;

use BeyondCapable\Entity\Blog\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * Class PostCrudController
 *
 * @package App\Controller\Admin
 */
class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title');
        yield TextareaField::new('body')->hideOnIndex()
            ->setNumOfRows(3)
            ->setHelp('Summaries can\'t contain Markdown or HTML contents; only plain text.');

        yield AssociationField::new('comments')->onlyOnIndex();
        yield DateTimeField::new('publicationDate');
        yield AssociationField::new('tags')->hideOnIndex();
    }
}
