<form class="ajax-update" action="{{ route('marks.update', [$exam_id, $my_course_id, $class_id, $subject_id]) }}"
      >
    @csrf @method('put')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>S/N</th>
            <th>Name</th>
            <th>ADM_NO</th>
            <th>1ST CA (20)</th>
            <th>2ND CA (20)</th>
            <th>EXAM (60)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($marks->sortBy('studentRecord.user_id') as $mk)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mk->studentRecord->user->id }} </td>
                <td>{{ $mk->studentRecord->adm_no }}</td>


                {{-- CA AND EXAMS --}}
                <td><input title="1ST CA" min="1" max="20" class="text-center" name="t1_{{ $mk->id }}"
                           value="{{ $mk->t1 }}" type="number"></td>
                <td><input title="2ND CA" min="1" max="20" class="text-center" name="t2_{{ $mk->id }}"
                           value="{{ $mk->t2 }}" type="number"></td>
                <td><input title="EXAM" min="1" max="60" class="text-center" name="exm_{{ $mk->id }}"
                           value="{{ $mk->exm }}" type="number"></td>

            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="text-center mt-2">
        <button type="submit" class="btn btn-primary">Update Marks <i class="icon-paperplane ml-2"></i></button>
    </div>
</form>
