<?php

namespace App\Repositories\Setting;

use App\Repositories\RepositoryInterface;

interface SettingRepositoryInterface extends RepositoryInterface
{

    public function update($id, $attribute);

    public function getSetting($type);
}
