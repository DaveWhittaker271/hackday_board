<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\Idea;
use core\entity\Interest;
use core\graphql\BaseResolver;
use core\util\Database;
use core\util\Users;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use GraphQL\Type\Definition\ResolveInfo;

class register_interest extends BaseResolver
{
    /**
     * @param $source
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     * @return bool
     * @throws Exception
     * @throws NotSupported
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public static function resolve($source, array $args, $context, ResolveInfo $info)
    {
        $user = Users::loggedIn();
        $em   = Database::entityManager();

        if (!$user) {
            return false;
        }

        $interest = $em->getRepository(Interest::class)->findOneBy(['idea_id' => $args['idea_id'], 'user_id'=> $user->id]);

        if (!$interest) {
            $interest = new Interest();

            $interest->user_id = $user->id;
            $interest->idea_id = $args['idea_id'];

            $em->persist($interest);
            $em->flush();
        }

        return true;
    }
}