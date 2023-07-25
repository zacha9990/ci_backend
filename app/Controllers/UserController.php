<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class UserController extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();

        return $this->respond($users);
    }

    public function create()
    {
        $userModel = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        if ($userModel->insert($data)) {
            return $this->respondCreated($data, 'User berhasil ditambahkan.');
        } else {
            return $this->fail('Gagal menambahkan user.');
        }
    }

    public function show($id = null)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user) {
            return $this->respond($user);
        } else {
            return $this->failNotFound('User tidak ditemukan.');
        }
    }

    public function update($id = null)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return $this->failNotFound('User tidak ditemukan.');
        }

        $data = [
            'username' => $this->request->getVar('username'),
            'email' => $this->request->getVar('email'),
        ];

        $userModel->update($id, $data);

        return $this->respondUpdated($data, 'Data user berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return $this->failNotFound('User tidak ditemukan.');
        }

        $userModel->delete($id);

        return $this->respondDeleted(['id' => $id], 'User berhasil dihapus.');
    }
}
