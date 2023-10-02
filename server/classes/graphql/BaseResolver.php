<?php

namespace core\graphql;

use GraphQL\Type\Definition\ResolveInfo;

abstract class BaseResolver
{
    public abstract static function resolve($source, array $args, $context, ResolveInfo $info);
}