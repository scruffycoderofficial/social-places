#!/usr/bin/env php
<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/../../vendor/autoload.php';

$config = new Configuration();

$url = "mysql://user:tester@db/capable_platform_db?serverVersion=5.7";

try {

    $conn = DriverManager::getConnection (array('url' => $url), $config);

    if ($conn->connect()) {
        echo "\n ====================\nConnection Successful\n====================";
    }

} catch (Exception $e) {
    echo "\n ==================== \n";
    echo $e->getMessage();
    echo "\n ====================";
}