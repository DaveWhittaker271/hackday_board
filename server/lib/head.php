<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use core\util\FilesHelper;

$dotenv = Dotenv::createImmutable(FilesHelper::basePath('../conf'));
$dotenv->load();
/*
$cache = \Symfony\Component\Cache\Adapter\PhpFilesAdapter(
    'doctrine_metadata',
    0,
    '/tmp/'
);
$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCache($cache);*/