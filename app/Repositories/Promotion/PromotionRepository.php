<?php

namespace App\Repositories\Promotion;

use App\Helpers\GetSystemInfoHelper;
use App\Models\Promotion;
use App\Repositories\BaseRepository;

class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Promotion::class;
    }
    public function getAll()
    {
        // TODO: Implement getAll() method.
        return $this->model->with(['student', 'fromCourse', 'toCourse', 'fromSection', 'toSection'])->where(['from_session' => GetSystemInfoHelper::getCurrentSession(), 'to_session' => GetSystemInfoHelper::getNextSession()])->get();

    }

    public function getPromotions(array $where)
    {
        // TODO: Implement getPromotions() method.
        return $this->model->where($where)->get();
    }

}
