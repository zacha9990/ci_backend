<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null)
    {     
        if (!verifyAuth($request)) {
            throw new \CodeIgniter\HTTP\Exceptions\HTTPException('Anda harus login terlebih dahulu.', 401);
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something after the request is handled
        // For example, modifying the response or headers
        // print_r($response); die();
        return $response;
    }
}
