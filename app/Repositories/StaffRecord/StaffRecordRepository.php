<?php

namespace App\Repositories\StaffRecord;

use App\Models\StaffRecord;
use App\Repositories\BaseRepository;


class StaffRecordRepository extends BaseRepository implements StaffRecordRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return StaffRecord::class;
    }


}
