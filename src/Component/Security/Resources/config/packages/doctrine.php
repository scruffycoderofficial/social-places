<?php

declare(strict_types=1);

use BeyondCapable\Component\Security\Persistence\Doctrine\Type\HashedPasswordType;

use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine): void {
    $doctrine->dbal()
        ->type(HashedPasswordType::NAME)
        ->class(HashedPasswordType::class);

    $doctrine->orm()
        ->entityManager('default')
            ->mapping('security')
                ->type('php')
                ->prefix('BeyondCapable\Component\Security\Domain\Entity')
                ->dir(__DIR__.'/../../mapping')
                ->isBundle(false);
};
