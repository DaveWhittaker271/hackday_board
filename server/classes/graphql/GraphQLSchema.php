<?php

namespace core\graphql;

use GraphQL\Error\Error;
use GraphQL\Error\SyntaxError;
use GraphQL\Language\Parser;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Utils\BuildSchema;
use GraphQL\Utils\SchemaExtender;
use GraphQL\Utils\SchemaPrinter;
use core\util\FilesHelper;

class GraphQLSchema
{
    private const CACHE_FILENAME = '/../tmp/cached_schema.php';

    /**
     * Get the compiled graphql schema, either from the cache or by building it directly
     *
     * @throws SyntaxError|Error
     */
    public static function getSchema(): Schema
    {
        if (!file_exists(self::CACHE_FILENAME) || (bool)$_ENV['CACHE_GRAPHQL_SCHEMA'] !== true) {
            return static::buildSchema();
        }

        $schemaData = file_get_contents(FilesHelper::BasePath(static::CACHE_FILENAME));
        $document   = Parser::parse($schemaData);

        return BuildSchema::build($document);
    }

    /**
     * @throws SyntaxError
     * @throws Error
     */
    public static function getSchemaString($rebuild = false): string
    {
        if ($rebuild || !file_exists(self::CACHE_FILENAME)) {
            static::buildSchema();
        }

        return file_get_contents(FilesHelper::BasePath(static::CACHE_FILENAME));
    }

    /**
     * Clear the graphql cache file
     *
     * @return void
     */
    public static function clearCache(): void
    {
        unlink(static::CACHE_FILENAME);
    }

    /**
     * Build & cache the compiled graphql schema
     *
     * @return Schema
     * @throws SyntaxError
     * @throws Error
     */
    private static function buildSchema(): Schema
    {
        $resolver = function ($value, array $args, $context, ResolveInfo $info): mixed {
            return GraphQLResolver::findResolverAndResolve($value, $args, $context, $info);
        };

        $config = SchemaConfig::create()
            ->setQuery(new ObjectType(['name' => 'Query', 'resolveField' => $resolver]))
            ->setMutation(new ObjectType(['name' => 'Mutation', 'resolveField' => $resolver]));

        $schema = new Schema($config);

        $files = array_merge(
            FilesHelper::filesInDirectory(FilesHelper::srcPath('graphql/mutation'), true),
            FilesHelper::filesInDirectory(FilesHelper::srcPath('graphql/query'), true)
        );

        $sources         = array_map('file_get_contents', $files);
        $extensionSource = implode("\n", $sources);
        $ast             = Parser::parse($extensionSource);
        $schema          = SchemaExtender::extend($schema, $ast);

        file_put_contents(FilesHelper::basePath(static::CACHE_FILENAME), SchemaPrinter::doPrint($schema));

        return $schema;
    }
}
