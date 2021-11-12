<?php

namespace App\Repositories\UserType;

use App\Models\UserType;
use App\Repositories\BaseRepository;

class UserTypeRepository extends BaseRepository implements UserTypeRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return UserType::class;
    }


}
