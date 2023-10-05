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
$projectId = $_POST['project_id'];

if (!empty($ideaId)) {
    $path .= 'ideas/' . $ideaId . '/';
} else if (!empty($projectId)) {
    $path .= 'projects/' . $projectId . '/';
} else {
    http_response_code(400);
    die();
}

$filesDir = FilesHelper::basePath('../files/');

if (!is_dir($filesDir . $path)) {
    mkdir($filesDir . $path, recursive: true);
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

        if (!empty($projectId)) {
            $file->project_id = $projectId;
        }

        $em = Database::entityManager();
        $em->persist($file);
        $em->flush();

        http_response_code(200);
    } else {
        http_response_code(400);
    }
}