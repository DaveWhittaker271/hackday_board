<?php

namespace core\util;

use core\entity\File;

class FilesHelper
{
    /**
     * Converts a file path to an absolute path
     *
     * @param string $path
     * @return string
     */
    public static function basePath(string $path = ''): string
    {
        if (substr($path, 0, 1) === '/') {
            $path = substr($path, 1);
        }

        $baseDir = $_SERVER['DOCUMENT_ROOT'] ?: getcwd() . '/server';

        return $baseDir . '/' . $path;
    }

    /**
     * Converts a file path to an absolute path towards the /src/ directory
     *
     * @param string $path
     * @return string
     */
    public static function srcPath(string $path = ''): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/../src/' . $path;
    }

    /**
     * List all files in the specified directory, optionally checking subdirectories too
     *
     * @param string $path
     * @param bool $checkSubdirectories
     * @return array
     */
    public static function filesInDirectory(string $path, bool $checkSubdirectories = false): array
    {
        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }

        $results = [];
        $files   = scandir($path);

        foreach ($files as $file) {
            $fullPath = $path . $file;
            if (is_dir($fullPath)) {
                if ($file !== '.' && $file !== '..' && $checkSubdirectories) {
                    $results = array_merge($results, self::filesInDirectory($fullPath, true));
                }
            } else {
                $results[] = $fullPath;
            }
        }

        return $results;
    }

    /**
     * @param File $file
     * @return string
     */
    public static function getUrlFromFile(File $file)
    {
        return $_ENV['BACKEND_HOSTNAME'] . '/file.php?id=' . $file->id;
    }
}