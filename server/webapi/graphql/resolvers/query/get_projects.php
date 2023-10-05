<?php

namespace webapi\graphql\resolvers\query;

use core\entity\Projects;
use core\entity\User;
use core\graphql\BaseResolver;
use core\util\Database;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use GraphQL\Type\Definition\ResolveInfo;

class get_projects extends BaseResolver
{
    /**
     * @param $source
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     * @return object
     * @throws Exception
     * @throws NotSupported
     * @throws ORMException
     */
    public static function resolve($source, array $args, $context, ResolveInfo $info)
    {
        $em = Database::entityManager();

        $projects = $em->getRepository(Projects::class)->findAll();

        foreach ($projects as &$project) {
            $user = $em->getRepository(User::class)->findOneBy(['id' => $project->submitted_by]);

            $project->submitted_by_name = $user->name;
        }

        return (object) ['projects' => $projects];
    }
}