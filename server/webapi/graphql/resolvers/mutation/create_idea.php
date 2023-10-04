<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\Idea;
use core\graphql\BaseResolver;
use core\util\Database;
use core\util\Users;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use GraphQL\Type\Definition\ResolveInfo;

class create_idea extends BaseResolver
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
    public static function resolve($source, array $args, $context, ResolveInfo $info): bool
    {
        $em      = Database::entityManager();

        $user = Users::loggedIn();

        $newIdea = new Idea();
        $newIdea->user_id    = $user->id;
        $newIdea->project_id = 1;
        $newIdea->title      = $args['title'];
        $newIdea->description = $args['description'];
        $newIdea->timecreated = time();

        $em->persist($newIdea);
        $em->flush();

        return true;
    }
}