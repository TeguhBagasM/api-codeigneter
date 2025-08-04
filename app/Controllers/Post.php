<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Post extends ResourceController
{
    protected $modelName = 'App\Models\PostModel';
    protected $format = 'json';

    /**
     * index function
     * @method : GET
     */
    public function index()
    {
        return $this->respond([
            'statusCode' => 200,
            'message'    => 'OK',
            'data'       => $this->model->orderBy('id', 'DESC')->findAll()
        ], 200);
    }

    /**
     * show function
     * @method : GET with params ID
     */
    public function show($id = null)
    {
        $data = $this->model->find($id);
        
        if (!$data) {
            return $this->failNotFound('Data tidak ditemukan');
        }

        return $this->respond([
            'statusCode' => 200,
            'message'    => 'OK',
            'data'       => $data
        ], 200);
    }

    /**
     * create function
     * @method : POST
     */
    public function create()
    {
        // Validasi input
        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Cek apakah request JSON atau form data
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $data = [
            'title'   => $input['title'],
            'content' => $input['content']
        ];

        $post = $this->model->insert($data);
        
        if ($post) {
            return $this->respondCreated([
                'statusCode' => 201,
                'message'    => 'Data berhasil disimpan!',
                'data'       => ['id' => $post]
            ]);
        }

        return $this->fail('Gagal menyimpan data');
    }

    /**
     * update function
     * @method : PUT or PATCH
     */
    public function update($id = null)
    {
        // Cek apakah data ada
        $existingPost = $this->model->find($id);
        if (!$existingPost) {
            return $this->failNotFound('Data tidak ditemukan');
        }

        // Validasi
        $rules = [
            'title' => 'required|min_length[3]',
            'content' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Get input data
        $input = $this->request->getJSON(true) ?? $this->request->getRawInput();
        
        $data = [
            'title'   => $input['title'],
            'content' => $input['content']
        ];

        $updated = $this->model->update($id, $data);

        if ($updated) {
            return $this->respond([
                'statusCode' => 200,
                'message'    => 'Data berhasil diupdate!',
            ]);
        }

        return $this->fail('Gagal mengupdate data');
    }

    /**
     * delete function
     * @method : DELETE with params ID
     */
    public function delete($id = null)
    {
        $post = $this->model->find($id);

        if (!$post) {
            return $this->failNotFound('Data tidak ditemukan');
        }

        $deleted = $this->model->delete($id);

        if ($deleted) {
            return $this->respondDeleted([
                'statusCode' => 200,
                'message'    => 'Data berhasil dihapus!',
            ]);
        }

        return $this->fail('Gagal menghapus data');
    }
}