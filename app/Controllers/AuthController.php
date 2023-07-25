<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use App\Services\UserService;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class AuthController extends Controller
{
    use ResponseTrait;

    protected $allowed_http_methods = array('get', 'delete', 'post', 'put');

    public function login()
    {
        // Ambil data input dari permintaan POST
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        if (empty($username) || empty($password)) {
            return $this->fail('Username dan password harus diisi.', 400);
        }

        // print_r($this->request->getPost()); die();

        // Cari pengguna berdasarkan username
        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Username atau password salah.');
        }

        // Jika autentikasi berhasil, hasilkan token JWT
        $key = 'random'; // Ganti dengan kunci rahasia Anda
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 jam (3600 detik) masa berlaku token

        $payload = array(
            'user_id' => $user['id'],
            'username' => $user['username'],
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );

        // Encode token menggunakan JWT::encode()
        $token = JWT::encode($payload, $key, 'HS256');

        // print_r([$payload, $token, $user['id'], ['token' => $token]]); die();

        // Simpan token di database untuk digunakan nanti
        $userModel->update($user['id'], ['token' => $token]);

        // Perbarui data user setelah simpan token
        $user = $userModel->find($user['id']);

        return $this->respond(['token' => $token, 'user' => $user, 'message' => 'Login berhasil.']);
    }
}
