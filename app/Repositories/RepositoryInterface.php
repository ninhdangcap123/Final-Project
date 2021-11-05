<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all
     * @return mixed
     */
    public function getAll();

    /**
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = []);

    /**
     * Update
     * @param $id
     * @param $attribute
     * @return mixed
     */
    public function update($id, $attribute);

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id);


}
