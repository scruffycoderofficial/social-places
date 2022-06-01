#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dbname = 'capable_platform_db';
$dbuser = 'user';
$dbpass = 'tester';
$dbhost = 'db';

$config = new \Doctrine\DBAL\Configuration();

$url = "mysql://{$dbuser}:{$dbpass}@{$dbhost}/{$dbname}?serverVersion=5.7";

$connectionParams = array('url' => $url);

try {

    $conn = \Doctrine\DBAL\DriverManager::getConnection ($connectionParams, $config);

    if ($conn->connect()) {
        echo "\n ==================== \n Connection Successful \n ====================";
    }
}

catch (Exception $e)
{
    echo "\n ==================== \n Connection unsuccessful \n ====================";
}