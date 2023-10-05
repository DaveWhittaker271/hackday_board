<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\Idea;
use core\graphql\BaseResolver;
use core\util\Database;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use GraphQL\Type\Definition\ResolveInfo;

class delete_idea extends BaseResolver
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
        if ($args['id'] === null) {
            return false;
        }

        $em   = Database::entityManager();
        $idea = $em->getRepository(Idea::class)->findOneBy(['id' => $args['id']]);

        $em->remove($idea);
        $em->flush();

        return true;
    }
}