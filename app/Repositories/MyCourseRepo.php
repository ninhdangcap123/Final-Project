<?php

namespace App\Repositories;

use App\Models\Major;
use App\Models\MyCourse;
use App\Models\Classes;
use App\Models\Subject;

class MyCourseRepo
{

    public function all()
    {
        return MyCourse::orderBy('name', 'asc')->with('major')->get();
    }

    public function getMC($data)
    {
        return MyCourse::where($data)->with('classes');
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

    /************* Classes *******************/

    public function createSection($data)
    {
        return Classes::create($data);
    }

    public function findSection($id)
    {
        return Classes::find($id);
    }

    public function updateSection($id, $data)
    {
        return Classes::find($id)->update($data);
    }

    public function deleteSection($id)
    {
        return Classes::destroy($id);
    }

    public function isActiveSection($class_id)
    {
        return Classes::where(['id' => $class_id, 'active' => 1])->exists();
    }

    public function getAllSections()
    {
        return Classes::orderBy('name', 'asc')->with(['myCourse', 'teacher'])->get();
    }

    public function getClassSections($course_id)
    {
        return Classes::where(['my_course_id' => $course_id])->orderBy('name', 'asc')->get();
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
        return Subject::orderBy('name', 'asc')->with(['myCourse', 'teacher'])->get();
    }

}
