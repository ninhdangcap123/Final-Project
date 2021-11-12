<?php

namespace App\Http\Controllers\MyParent;
use App\Http\Controllers\Controller;
use App\Repositories\Student\StudentRepositoryInterface;
use App\Repositories\StudentRepo;
use Illuminate\Support\Facades\Auth;

class MyController extends Controller
{
    protected $studentRepo;
    public function __construct(StudentRepositoryInterface $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function children()
    {
        $data['students'] = $this->studentRepo->getRecord(['my_parent_id' => Auth::user()->id])->with(['myCourse', 'classes'])->get();

        return view('pages.parent.children', $data);
    }

}
