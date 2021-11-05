<?php

namespace App\Repositories\Promotion;

use App\Repositories\RepositoryInterface;

interface PromotionRepositoryInterface extends RepositoryInterface
{
    public function create($attributes = []);
    public function find($id);
    public function delete($id);
    public function getAll();
    public function getPromotions(array $where);

}
