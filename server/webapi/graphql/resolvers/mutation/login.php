<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\User;
use core\util\Auth;
use core\util\Database;
use core\util\Users;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use GraphQL\Type\Definition\ResolveInfo;
use core\graphql\BaseResolver;

class login extends BaseResolver
{
    /**
     * @param $source
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     * @return string
     * @throws Exception
     * @throws ORMException
     */
    public static function resolve($source, array $args, $context, ResolveInfo $info): string
    {
        $tokenData = Auth::getDataFromToken($args['jwt_token']);

        // Token valid, we should check if we have a matching user saved in the DB
        $em = Database::entityManager();

        $user = Users::getUserFromGoogleID($tokenData['sub']);

        // If no user is found we'll create a new one
        if (empty($user)) {
            $user = new User();
            $user->google_uid = $tokenData['sub'];
        }

        $user->email = $tokenData['email'];
        $user->name = $tokenData['name'];
        $user->picture_url = $tokenData['picture'];

        $em->persist($user);
        $em->flush();

        return true;
    }
}