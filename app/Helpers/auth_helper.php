<?php

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\IncomingRequest;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function failUnauthorized(IncomingRequest $request, $message = null)
{
    $response = service('response'); // Create Response instance using the global Services class

    return $response->setStatusCode(401)->setJSON(['error' => 'Unauthorized', 'message' => $message]);
}

function generateJWTToken(array $data)
{
    $key = 'random'; 
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Token will be valid for 1 hour (3600 seconds)
    
    $payload = array(
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => $data
    );
    
    return JWT::encode($payload, $key);
}

function verifyAuth($request)
{
    $token = $request->getServer('HTTP_AUTHORIZATION');


    if (!$token) {
        failUnauthorized($request, 'Token tidak ada.')->send();
        exit();
    }

    try {
        $key = 'random'; // Replace with your actual secret key
        $decodedToken = JWT::decode($token, new Key($key, 'HS256'));
    } catch (\Exception $e) {
        // print_r($e);
        failUnauthorized($request, 'Token tidak valid.')->send();
        exit(); // Stop further processing
    }
    return true; // Authentication successful
}
