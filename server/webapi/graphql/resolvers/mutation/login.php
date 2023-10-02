<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\User;
use core\util\Authenticator;
use core\util\Database;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Google_Client;
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
        $token = $args['jwt_token'];

        $client = new Google_Client(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]);

        $payload = $client->verifyIdToken($token);

        // Invalid token
        if (!$payload) {
            return false;
        }

        // Token valid, we should check if we have a matching user saved in the DB
        $em = Database::entityManager();

        $user = $em->getRepository(User::class)->findOneBy(['google_uid' => $payload['sub']]);

        // If no user is found we'll create a new one
        if (empty($user)) {
            $user = new User();
            $user->google_uid = $payload['sub'];
        }

        $user->email = $payload['email'];
        $user->name = $payload['name'];
        $user->picture_url = $payload['picture'];

        $em->persist($user);
        $em->flush();

        return true;
    }
}