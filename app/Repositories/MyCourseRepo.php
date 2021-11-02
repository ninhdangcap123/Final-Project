<?php

namespace App\Repositories;

use App\Models\Major;
use App\Models\MyCourse;
use App\Models\Section;
use App\Models\Subject;

class MyCourseRepo
{

    public function all()
    {
        return MyCourse::orderBy('name', 'asc')->with('major')->get();
    }

    public function getMC($data)
    {
        return MyCourse::where($data)->with('section');
    }

    public function find($id)
    {
        return MyCourse::find($id);
    }

    public function create($data)
    {
        return MyCourse::create($data);
    }

    public function update($id, $data)
    {
        return MyCourse::find($id)->update($data);
    }

    public function delete($id)
    {
        return MyCourse::destroy($id);
    }

    public function getMajor()
    {
        return Major::orderBy('name', 'asc')->get();
    }

    public function findType($major_id)
    {
        return Major::find($major_id);
    }

    public function findTypeByClass($course_id)
    {
        return Major::find($this->find($course_id)->major_id);
    }

    /************* Section *******************/

    public function createSection($data)
    {
        return Section::create($data);
    }

    public function findSection($id)
    {
        return Section::find($id);
    }

    public function updateSection($id, $data)
    {
        return Section::find($id)->update($data);
    }

    public function deleteSection($id)
    {
        return Section::destroy($id);
    }

    public function isActiveSection($section_id)
    {
        return Section::where(['id' => $section_id, 'active' => 1])->exists();
    }

    public function getAllSections()
    {
        return Section::orderBy('name', 'asc')->with(['my_course', 'teacher'])->get();
    }

    public function getClassSections($course_id)
    {
        return Section::where(['my_course_id' => $course_id])->orderBy('name', 'asc')->get();
    }

    /************* Subject *******************/

    public function createSubject($data)
    {
        return Subject::create($data);
    }

    public function findSubject($id)
    {
        return Subject::find($id);
    }

    public function findSubjectByClass($course_id, $order_by = 'name')
    {
        return $this->getSubject(['my_course_id'=> $course_id])->orderBy($order_by)->get();
    }

    public function findSubjectByTeacher($teacher_id, $order_by = 'name')
    {
        return $this->getSubject(['teacher_id'=> $teacher_id])->orderBy($order_by)->get();
    }

    public function getSubject($data)
    {
        return Subject::where($data);
    }

    public function getSubjectsByIDs($ids)
    {
        return Subject::whereIn('id', $ids)->orderBy('name')->get();
    }

    public function updateSubject($id, $data)
    {
        return Subject::find($id)->update($data);
    }

    public function deleteSubject($id)
    {
        return Subject::destroy($id);
    }

    public function getAllSubjects()
    {
        return Subject::orderBy('name', 'asc')->with(['my_course', 'teacher'])->get();
    }

}
