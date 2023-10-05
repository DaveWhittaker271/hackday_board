<?php

namespace core\util;

use core\entity\User;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;

class Users
{
    /**
     * @return User
     * @throws Exception
     * @throws NotSupported
     * @throws ORMException
     */
    public static function loggedIn(): User
    {
        global $_SERVER;

        if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {
            API::invalidResponse('No token sent');
        }

        $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);

        if (empty($token)) {
            API::invalidResponse('No token found');
        }

        $payload = Auth::getDataFromToken($token);

        if (empty($payload)) {
            API::invalidResponse('Token invalid');
        }

        $user = static::getUserFromGoogleID($payload['sub']);

        if (empty($user)) {
            API::invalidResponse('No user found for token');
        }

        return $user;
    }

    /**
     * @param string $googleUid
     * @return User|null
     * @throws Exception
     * @throws NotSupported
     * @throws ORMException
     */
    public static function getUserFromGoogleID(string $googleUid): ?User
    {
        $em = Database::entityManager();

        return $em->getRepository(User::class)->findOneBy(['google_uid' => $googleUid]);
    }
}