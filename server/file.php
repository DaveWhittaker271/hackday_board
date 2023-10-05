<?php

require __DIR__ . '/lib/head.php';

use core\entity\File;
use core\util\Database;
use core\util\FilesHelper;

$fileId = $_GET['id'];

if (empty($fileId)) {
    http_response_code(404);
    die();
}

$em = Database::entityManager();

$file = $em->getRepository(File::class)->findOneBy(['id' => $fileId]);

if (empty($file)) {
    http_response_code(404);
    die();
}

$path = FilesHelper::basePath('/../files/' . $file->path);

if (file_exists($path)) {
    $mimeType = mime_content_type($path);
    header('Content-Type: ' . $mimeType);
    ob_clean();
    flush();
    readfile($path);
    exit;
}
?>