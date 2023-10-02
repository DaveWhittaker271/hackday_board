<?php

namespace core\util;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\Driver\PHPDriver;

class Database
{
    private static ?Connection $conn = null;
    private static ?EntityManager $em = null;

    /**
     * @return Connection
     * @throws Exception
     */
    public static function connection(): Connection
    {
        if (!static::$conn) {
            static::$conn = DriverManager::getConnection([
                'driver'   => 'pdo_mysql',
                'host'     => $_ENV['DB_HOST'],
                'user'     => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
                'dbname'   => $_ENV['DB_NAME'],
            ]);
        }

        return static::$conn;
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public static function queryBuilder(): QueryBuilder
    {
        return static::entityManager()->createQueryBuilder();
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public static function entityManager(): EntityManager
    {
        if (!static::$em) {
            $config = ORMSetup::createConfiguration(proxyDir: '/tmp/proxy');

            $namespaces = [FilesHelper::basePath('classes/entity/mapping/') => 'core\\entity'];
            $driver = new \Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver($namespaces);

            $config->setMetadataDriverImpl($driver);

            static::$em = new EntityManager(static::connection(), $config);
        }

        return static::$em;
    }
}