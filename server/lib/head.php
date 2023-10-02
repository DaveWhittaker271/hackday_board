<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use core\util\FilesHelper;

$dotenv = Dotenv::createImmutable(FilesHelper::basePath('../conf'));
$dotenv->load();

if (!is_dir(FilesHelper::basePath('/tmp'))) {
    mkdir(FilesHelper::basePath('/tmp'));
}

if (!is_dir(FilesHelper::srcPath('/graphql/query'))) {
    mkdir(FilesHelper::srcPath('/graphql/query'));
}