<?php

declare(strict_types=1);

use BeyondCapable\Core\Platform\Persistence\Doctrine\Type\DateTimeType;
use BeyondCapable\Core\Platform\Persistence\Doctrine\Type\UuidTokenType;
use BeyondCapable\Core\Platform\Persistence\Doctrine\Type\EmailAddressType;
use BeyondCapable\Core\Platform\Persistence\Doctrine\Type\UuidIdentifierType;
use BeyondCapable\Component\Security\Persistence\Doctrine\Type\HashedPasswordType;

$metadata->mapField([
    'id' => true,
    'fieldName' => 'identifier',
    'type' => UuidIdentifierType::NAME,
]);

$metadata->mapField([
    'fieldName' => 'email',
    'type' => EmailAddressType::NAME,
]);

$metadata->mapField([
    'fieldName' => 'hashedPassword',
    'type' => HashedPasswordType::NAME,
]);

$metadata->mapField([
    'fieldName' => 'expiredAt',
    'type' => DateTimeType::NAME,
    'nullable' => true,
]);

$metadata->mapField([
    'fieldName' => 'suspendedAt',
    'type' => DateTimeType::NAME,
    'nullable' => true,
]);

$metadata->mapField([
    'fieldName' => 'forgottenPasswordRequestedAt',
    'type' => DateTimeType::NAME,
    'nullable' => true,
]);

$metadata->mapField([
    'fieldName' => 'forgottenPasswordToken',
    'type' => UuidTokenType::NAME,
    'nullable' => true,
]);
