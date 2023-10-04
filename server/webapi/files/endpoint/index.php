<?php

use core\util\FilesHelper;
use core\util\Users;

require $_SERVER['DOCUMENT_ROOT'] . '/lib/head.php';

header('Access-Control-Allow-Headers: Content-Type, Authorization');

$currentUser = Users::loggedIn();

$filesDir = FilesHelper::basePath('files/');

if (!is_dir($filesDir)) {
    mkdir($filesDir);
}

foreach ($_FILES as $file) {
    $fileDest = $filesDir . basename($file["name"]);

    if (move_uploaded_file($file["tmp_name"], $fileDest)) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
}