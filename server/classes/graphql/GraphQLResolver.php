<?php

namespace core\graphql;

use Error;
use Exception;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error as GraphQLError;
use GraphQL\Error\SyntaxError;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use core\util\FilesHelper;

class GraphQLResolver
{
    /**
     * @throws SyntaxError|GraphQLError
     */
    public static function resolve(): void
    {
        header('Access-Control-Allow-Origin: ' . $_ENV['FRONTEND_HOSTNAME']);
        header('Access-Control-Allow-Headers: Content-Type, GraphQL-Resolver, HTTP_AUTHORIZATION');
        header('Access-Control-Allow-Methods: POST, OPTIONS');

        $schema   = GraphQLSchema::getSchema();
        $rawInput = file_get_contents('php://input');
        $input    = json_decode($rawInput, true);

        $variableValues = isset($input['variables']) ? $input['variables'] : null;

        // If this is a CORS check we can end early
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            die();
        }

        header('Content-Type: application/json');

        // Validate the query parameters
        if (empty($input)) {
            die();
        }

        if (!array_key_exists('query', $input)) {
            die('No query schema');
        }

        $query = $input['query'];

        try {
            // Execute the query
            $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);

            // Return the query results, with or without debugging
            if ((bool)$_ENV['GRAPHQL_DEBUG'] === true) {
                $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE);
            } else {
                $output = $result->toArray();
            }

            echo json_encode($output);
        } catch (GraphQLError|Error|Exception $e) {
            echo json_encode([
                'errors' => [
                    ['message' => $e->getMessage()]
                ]
            ]);
        }
    }

    /**
     * Imports the query resolver class from the query name
     *
     * @param $value
     * @param array $args
     * @param $context
     * @param ResolveInfo $info
     * @return array
     */
    public static function findResolverAndResolve($value, array $args, $context, ResolveInfo $info)
    {
        $queryName = $info->fieldName;

        $resolverPath = $info->operation->operation;
        $classPath    = "webapi/graphql/resolvers/$resolverPath/$queryName.php";

        require FilesHelper::basePath($classPath);

        $classNameSpace = "\\webapi\\graphql\\resolvers\\$resolverPath\\$queryName";

        $instance = new $classNameSpace();

        return $instance->resolve($value, $args, $context, $info);
    }
}

?>