<?php

namespace App\Repositories\LGA;

use App\Repositories\RepositoryInterface;

interface LGARepositoryInterface extends RepositoryInterface
{
    public function getAllLGAs($state_id);

}
