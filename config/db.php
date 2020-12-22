<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=test_wdcom_db',
    'username' => 'test_wdcom_user',
    'password' => 'apX21doixiJs1',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
    'attributes' => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));",
    ],
];

// CREATE DATABASE test_wdcom_db CHARACTER SET utf8 COLLATE utf8_unicode_ci;
// CREATE USER 'test_wdcom_user'@'localhost' IDENTIFIED BY 'apX21doixiJs1';
// GRANT ALL PRIVILEGES ON test_wdcom_db.* TO 'test_wdcom_user'@'localhost';
