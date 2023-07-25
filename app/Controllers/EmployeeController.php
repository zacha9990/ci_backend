<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;
use Firebase\JWT\JWT;

class EmployeeController extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\EmployeeModel';
    protected $format    = 'json';

    public function index()
    {
        $employeeModel = new EmployeeModel();
        $employees = $employeeModel->findAll();

        return $this->respond($employees);
    }

    public function create()
    {
        $name = $this->request->getPost('name');
        $position = $this->request->getPost('position');

        // Check if the name and position fields are empty
        if (empty($name) || empty($position)) {
            return $this->fail('Nama dan posisi pegawai harus diisi.');
        }

        $photo = $this->request->getFile('photo');

        // Check if a photo is uploaded
        if ($photo !== null && $photo->isValid() && !$photo->hasMoved()) {
            // Proses upload foto dengan batasan ukuran maksimal 300KB
            if ($photo->getSize() > 300 * 1024) {
                return $this->fail('Ukuran file foto melebihi batas maksimal 300KB.');
            }

            $newName = $photo->getRandomName();
            $photo->move(ROOTPATH . 'public/uploads', $newName);
        } else {
            // If no photo is uploaded, set the $newName to null
            $newName = null;
        }

        // Simpan data pegawai beserta nama file foto (jika ada) ke database
        $employeeModel = new EmployeeModel();
        $data = [
            'name' => $name,
            'position' => $position,
            'photo' => $newName,
        ];
        $employeeModel->insert($data);

        return $this->respondCreated($data, 'Pegawai berhasil ditambahkan.');
    }


    public function update($id = null)
{
    $employeeModel = new EmployeeModel();
    $employee = $employeeModel->find($id);

    if (!$employee) {
        return $this->failNotFound('Pegawai tidak ditemukan.');
    }

    $photo = $this->request->getFile('photo');

    // Check if a photo is uploaded and valid
    if ($photo !== null && $photo->isValid() && !$photo->hasMoved()) {
        // Proses upload foto dengan batasan ukuran maksimal 300KB
        if ($photo->getSize() > 300 * 1024) {
            return $this->fail('Ukuran file foto melebihi batas maksimal 300KB.');
        }

        $newName = $photo->getRandomName();
        $photo->move(ROOTPATH . 'public/uploads', $newName);
        
        // Update the photo name in the data
        $data['photo'] = $newName;
    }

    // Update other employee data
    $data['name'] = $this->request->getVar('name');
    $data['position'] = $this->request->getVar('position');

    $employeeModel->update($id, $data);

    return $this->respondUpdated($data, 'Data pegawai berhasil diperbarui.');
}

    


    public function show($id = null)
    {
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($id);

        if ($employee) {
            return $this->respond($employee);
        } else {
            return $this->failNotFound('Pegawai tidak ditemukan.');
        }
    }

    public function delete($id = null)
    {
        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($id);

        if (!$employee) {
            return $this->failNotFound('Pegawai tidak ditemukan.');
        }

        $employeeModel->delete($id);

        return $this->respondDeleted(['id' => $id], 'Pegawai berhasil dihapus.');
    }
}
