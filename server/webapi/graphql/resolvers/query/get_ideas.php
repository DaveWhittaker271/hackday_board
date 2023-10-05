<?php

namespace webapi\graphql\resolvers\query;

use core\entity\File;
use core\entity\Idea;
use core\entity\User;
use core\graphql\BaseResolver;
use core\util\Database;
use core\util\FilesHelper;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Google\Service\Drive\Resource\Files;
use GraphQL\Type\Definition\ResolveInfo;

class get_ideas extends BaseResolver
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

        $ideas = $em->getRepository(Idea::class)->findBy(['project_id' => $args['project_id']]);

        foreach ($ideas as &$idea) {
            $user = $em->getRepository(User::class)->findOneBy(['id' => $idea->user_id]);

            $idea->user_name =  $user->name;
            $idea->user_image = $user->picture_url;

            $files = $em->getRepository(File::class)->findBy(['idea_id' => $idea->id]);

            if (!empty($files)) {
                $firstFile = current($files);
                $idea->picture_url = FilesHelper::getUrlFromFile($firstFile);
                $idea->files = array_map(function ($file) {
                    return FilesHelper::getUrlFromFile($file);
                }, $files);
            }
        }

        return (object) ['ideas' => $ideas];
    }
}