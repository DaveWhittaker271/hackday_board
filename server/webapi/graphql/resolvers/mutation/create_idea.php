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
     * @return int
     * @throws Exception
     * @throws NotSupported
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public static function resolve($source, array $args, $context, ResolveInfo $info): int
    {
        $em   = Database::entityManager();
        $user = Users::loggedIn();

        if ($args['id']) {
            $idea = $em->getRepository(Idea::class)->findOneBy(['id' => $args['id'], 'user_id' => $user->id]);
        } else {
            $idea = new Idea();
        }

        $idea->user_id    = $user->id;
        $idea->project_id = $args['project_id'];
        $idea->title      = $args['title'];
        $idea->description = $args['description'];
        $idea->timecreated = time();

        $em->persist($idea);
        $em->flush();

        return $idea->id;
    }
}