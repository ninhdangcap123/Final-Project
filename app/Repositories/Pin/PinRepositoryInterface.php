<?php

namespace App\Repositories\Pin;

use App\Repositories\RepositoryInterface;

interface PinRepositoryInterface extends RepositoryInterface
{
    public function find($id);
    public function create($attributes = []);
    public function update($id, $attribute);
    public function countValid();
    public function deleteUsed();
    public function getUserPin($code, $user_id, $st_id);
    public function findValidCode($code);
    public function getValid();
    public function getInvalid();
    public function insert($attributes = []);


}
