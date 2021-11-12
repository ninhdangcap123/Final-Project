<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;
use Ramsey\Collection\Set;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Setting::class;
    }

    public function update($id, $attribute)
    {
        return $this->model->where('type',$id)->update(['description'=>$attribute]);
    }
    public function getSetting($type)
    {
        // TODO: Implement getSetting() method.
        return $this->model->where('type', $type)->get();
    }

}
