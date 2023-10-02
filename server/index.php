<?php

require __DIR__ . '/lib/head.php';

use core\graphql\GraphQLSchema;

echo "Regenerated GraphQL Schema:";
echo "<br />";
echo "<br />";
echo "<pre>" . nl2br(GraphQLSchema::getSchemaString(true)) . "</pre>";