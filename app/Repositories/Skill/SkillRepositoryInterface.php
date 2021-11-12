<?php

namespace App\Repositories\Skill;

use App\Repositories\RepositoryInterface;

interface SkillRepositoryInterface extends RepositoryInterface
{
    public function getSkill($where);

    public function getSkillByMajor($major = NULL, $skill_type = NULL);

}
