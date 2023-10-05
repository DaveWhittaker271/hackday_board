<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\Projects;
use core\graphql\BaseResolver;
use core\util\Database;
use core\util\Users;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use GraphQL\Type\Definition\ResolveInfo;

class create_project extends BaseResolver
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
            $project = $em->getRepository(Projects::class)->findOneBy(['id' => $args['id'], 'user_id' => $user->id]);
        } else {
            $project = new Projects();
        }

        $project->title        = $args['title'];
        $project->description  = $args['description'];
        $project->submitted_by = $user->id;

        $em->persist($project);
        $em->flush();

        return $project->id;
    }
}