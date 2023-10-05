<?php

use core\entity\File;
use core\util\Database;
use core\util\FilesHelper;
use core\util\Users;

require $_SERVER['DOCUMENT_ROOT'] . '/lib/head.php';

header('Access-Control-Allow-Headers: Content-Type, Authorization');

$currentUser = Users::loggedIn();

$path = $currentUser->id  . '/';

$ideaId = $_POST['idea_id'];

if (!empty($ideaId)) {
    $path .= 'ideas/' . $ideaId . '/';
} else {
    http_response_code(400);
    die();
}

$filesDir = FilesHelper::basePath('../files/');

if (!is_dir($filesDir)) {
    mkdir($filesDir, recursive: true);
}

foreach ($_FILES as $file) {
    $path .= basename($file["name"]);
    $fileDest = $filesDir . $path;

    if (move_uploaded_file($file["tmp_name"], $fileDest)) {
        $file = new File();
        $file->path = $path;
        $file->user_id = $currentUser->id;
        $file->timecreated = time();

        if (!empty($ideaId)) {
            $file->idea_id = $ideaId;
        }

        $em = Database::entityManager();
        $em->persist($file);
        $em->flush();

        http_response_code(200);
    } else {
        http_response_code(400);
    }
}