<?php

namespace core\util;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;

class API
{
    /**
     * @param string $reason
     * @return void
     */
    public static function invalidResponse(string $reason = ''): void
    {
        if (!empty($reason)) {
            echo $reason;
        }

        http_response_code(403);
        die();
    }

    /**
     * @return void
     * @throws Exception
     * @throws NotSupported
     * @throws ORMException
     */
    public static function requireLogin(): void
    {
       Users::loggedIn();
    }
}