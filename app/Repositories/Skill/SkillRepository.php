<?php

namespace App\Repositories\Skill;

use App\Models\Skill;
use App\Repositories\BaseRepository;

class SkillRepository extends BaseRepository implements SkillRepositoryInterface
{
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Skill::class;
    }

    public function getSkill($where)
    {
        // TODO: Implement getSkill() method.
        return $this->model->where($where)->orderBy('name')->get();

    }

    public function getSkillByMajor($major = NULL, $skill_type = NULL)
    {
        // TODO: Implement getSkillByMajor() method.
        return ($skill_type)
            ? $this->getSkill(['major' => $major, 'skill_type' => $skill_type])
            : $this->getSkill(['major' => $major]);
    }
}
