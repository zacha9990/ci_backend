<?php

namespace App\Services;

use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use CodeIgniter\HTTP\RequestInterface;

class UserService
{
    use ResponseTrait;

    protected $userModel;
    protected $request;

    public function __construct(RequestInterface $request)
    {
        $this->userModel = new UserModel();
        $this->request = $request;
    }

    public function isLoggedIn()
    {
        $token = $this->request->getServer('HTTP_AUTHORIZATION');

        if (!$token) {
            return false;
        }

        try {
            $key = 'random'; // Ganti dengan kunci rahasia Anda
            JWT::decode($token, $key, array('HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function login($username, $password)
    {
        // Cari pengguna berdasarkan username
        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return ['status' => 'error', 'message' => 'Username tidak ditemukan.'];
        }

        // Verifikasi password menggunakan password_verify()
        if (password_verify($password, $user['password'])) {
            // Autentikasi berhasil, atur status login dan ID pengguna ke dalam session
            session()->set('isLoggedIn', true);
            session()->set('userId', $user['id']);

            return ['status' => 'success', 'data' => $user, 'message' => 'Login berhasil.'];
        } else {
            return ['status' => 'error', 'message' => 'Password salah.'];
        }
    }
}
