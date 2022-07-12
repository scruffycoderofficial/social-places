#!/usr/bin/env php
<?php

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$transport = Transport::fromDsn('smtp://mailhog:1025');

$mailer = new Mailer($transport);

$email = (new Email())
    ->from('sender@example.test')
    ->to('your-email@here.test')
    ->priority(Email::PRIORITY_HIGHEST)
    ->subject('My first mail using Symfony Mailer')
    ->text('This is an important message!')
    ->html('<strong>This is an important message!</strong>');

try {

    $mailer->send($email);

} catch (TransportExceptionInterface $e) {
    echo "\n ==================== Begin Error Message ====================\n";
    echo $e->getMessage();
    echo "\n ==================== End Error Message ====================";
}