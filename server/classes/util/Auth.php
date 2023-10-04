<?php

namespace core\util;

use Google_Client;

class Auth
{
    public static function getDataFromToken(string $token)
    {
        $client = new Google_Client(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]);

        $payload = $client->verifyIdToken($token);

        // Invalid or expired token
        if (!$payload) {
            return false;
        }

        return $payload;
    }
}