<?php

namespace webapi\graphql\resolvers\mutation;

use core\entity\Comment;
use core\graphql\BaseResolver;
use core\util\Database;
use core\util\Users;
use GraphQL\Type\Definition\ResolveInfo;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;

class add_comment extends BaseResolver
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
        $comment = new Comment();
        $em      = Database::entityManager();
        $user    = Users::loggedIn();

        if (!$user) {
            return false;
        }

        $comment->user_id     = $user->id;
        $comment->idea_id     = $args['idea_id'];
        $comment->text        = $args['text'];
        $comment->timecreated = time();

        $em->persist($comment);
        $em->flush();

        return true;
    }
}