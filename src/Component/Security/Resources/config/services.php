<?php

declare(strict_types=1);

use BeyondCapable\Component\Security\Core\PasswordHasher\PasswordHasher;

use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container = $container->services()->defaults()
        ->bind('string $emailNoReply', param('mailer.no_reply'))
        ->public()
        ->autoconfigure()
        ->autowire();

    $container
        ->load('BeyondCapable\\Component\\Security\\', __DIR__.'/../../../../../../')
        ->exclude([
            __DIR__.'/../',
            __DIR__.'/../../Domain/ValueObject',
            __DIR__.'/../../Domain/Entity',
            __DIR__.'/../../Domain/Tests',
            __DIR__.'/../../Domain/UseCase/**/*Output.php',
            __DIR__.'/../../Presenter/ViewModel',
            __DIR__.'/../../Presenter/Input',
            __DIR__.'/../../Presenter/Controller',
            __DIR__.'/../../Core/User/UserProxy.php',
            __DIR__.'/../../Core/Authenticator/Passport/PasswordCredentials.php',
            __DIR__.'/../../Core/Tests',
        ]);

    $container
        ->load('BeyondCapable\\Component\\Security\\Presenter\\Controller\\', __DIR__.'/../../Presenter/Controller')
        ->tag('controller.service_arguments');

    $container->set(NativePasswordHasher::class);

    $container->set(PasswordHasher::class)
        ->decorate(NativePasswordHasher::class)
        ->args([service('.inner')]);
};